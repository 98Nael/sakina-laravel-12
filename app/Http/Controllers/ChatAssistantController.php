<?php

namespace App\Http\Controllers;

use App\Models\Doctor\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAssistantController extends Controller
{
    /**
     * Handle chat assistant requests from landing page.
     */
    public function respond(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|min:2|max:2000',
            'history' => 'nullable|array|max:12',
            'history.*.role' => 'required|in:user,assistant',
            'history.*.text' => 'required|string|max:2000',
        ]);

        $message = trim($validated['message']);
        $history = collect($validated['history'] ?? [])
            ->map(function (array $item) {
                return [
                    'role' => $item['role'],
                    'text' => trim($item['text']),
                ];
            })
            ->filter(fn (array $item) => $item['text'] !== '')
            ->values()
            ->all();

        $assessment = $this->assessMessage($message);
        $recommendedDoctors = $this->recommendDoctors($assessment['recommended_specialty']);
        $reply = $this->generateAssistantReply($message, $history, $assessment, $recommendedDoctors);

        return response()->json([
            'reply' => $reply,
            'triage' => $assessment,
            'recommended_doctors' => $recommendedDoctors,
            'disclaimer' => 'هذه المساعدة أولية ولا تغني عن التقييم الطبي المباشر.',
        ]);
    }

    /**
     * Rule-based initial triage.
     */
    private function assessMessage(string $message): array
    {
        $text = mb_strtolower($message, 'UTF-8');

        $urgentKeywords = [
            'انتحار',
            'انهي حياتي',
            'قتل نفسي',
            'self-harm',
            'suicide',
            'kill myself',
            'هلاوس',
            'hallucination',
            'لا استطيع التنفس',
            'chest pain',
            'panic attack',
        ];

        $moderateKeywords = [
            'اكتئاب',
            'قلق',
            'توتر',
            'خوف',
            'نوبات هلع',
            'insomnia',
            'anxiety',
            'depression',
            'sleep',
            'trauma',
        ];

        $specialtyMap = [
            'طب نفسي' => ['اكتئاب', 'قلق', 'انتحار', 'panic', 'depression', 'anxiety', 'ذهان', 'هلاوس'],
            'علاج نفسي' => ['جلسات', 'therapy', 'ضغوط', 'توتر', 'stress', 'social anxiety'],
            'أعصاب' => ['صداع', 'migraine', 'seizure', 'epilepsy', 'دوخة'],
            'طب عام' => ['تعب', 'حمى', 'pain', 'fatigue', 'nausea'],
        ];

        $isUrgent = $this->containsAny($text, $urgentKeywords);
        $isModerate = !$isUrgent && $this->containsAny($text, $moderateKeywords);

        $severity = $isUrgent ? 'high' : ($isModerate ? 'medium' : 'low');

        $recommendedSpecialty = 'طب نفسي';
        foreach ($specialtyMap as $specialty => $keywords) {
            if ($this->containsAny($text, $keywords)) {
                $recommendedSpecialty = $specialty;
                break;
            }
        }

        $notes = match ($severity) {
            'high' => 'مؤشرات تستدعي تدخلًا سريعًا وطلب مساعدة فورية.',
            'medium' => 'تحتاج متابعة قريبة مع مختص خلال وقت قصير.',
            default => 'يمكن البدء بدعم أولي مع حجز موعد تقييم.',
        };

        return [
            'severity' => $severity,
            'is_urgent' => $isUrgent,
            'recommended_specialty' => $recommendedSpecialty,
            'notes' => $notes,
        ];
    }

    /**
     * Recommend verified doctors by specialization.
     */
    private function recommendDoctors(string $specialty): array
    {
        $query = Doctor::query()->where('verified', true);

        $likeTerms = match ($specialty) {
            'علاج نفسي' => ['%psych%', '%therapy%', '%نفسي%'],
            'أعصاب' => ['%neuro%', '%أعصاب%'],
            'طب عام' => ['%general%', '%طب عام%'],
            default => ['%psy%', '%mental%', '%نفسي%'],
        };

        $query->where(function ($q) use ($likeTerms) {
            foreach ($likeTerms as $term) {
                $q->orWhere('specialization', 'like', $term);
            }
        });

        $doctors = $query
            ->orderByDesc('rating')
            ->latest()
            ->take(3)
            ->get();

        if ($doctors->isEmpty()) {
            $doctors = Doctor::query()
                ->where('verified', true)
                ->orderByDesc('rating')
                ->latest()
                ->take(3)
                ->get();
        }

        return $doctors->map(function (Doctor $doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
                'hospital_name' => $doctor->hospital_name,
            ];
        })->values()->all();
    }

    /**
     * Generate assistant response via OpenAI with fallback.
     */
    private function generateAssistantReply(string $message, array $history, array $assessment, array $recommendedDoctors): string
    {
        $apiKey = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-4o-mini');

        if (!$apiKey) {
            return $this->fallbackReply($assessment);
        }

        $historyText = collect($history)
            ->take(-8)
            ->map(fn (array $item) => strtoupper($item['role']) . ': ' . $item['text'])
            ->implode("\n");

        $doctorList = collect($recommendedDoctors)
            ->map(fn (array $doctor) => ($doctor['name'] ?? '-') . ' - ' . ($doctor['specialization'] ?? '-'))
            ->implode("; ");

        $systemPrompt = <<<PROMPT
You are Sakina Care Assistant in Arabic.
Your goals:
1) Provide calm emotional first support.
2) Give a short initial non-diagnostic assessment.
3) Suggest next safe steps.
4) Direct the user to the right doctor specialty.

Rules:
- Do NOT provide definitive diagnosis.
- Do NOT prescribe medications or dosages.
- If self-harm/suicide/high-risk signals appear, clearly advise immediate emergency help and trusted person contact.
- Keep response concise, warm, and practical.
- Return plain Arabic text only.
PROMPT;

        $userPrompt = <<<PROMPT
Current user message:
{$message}

Recent history:
{$historyText}

Preliminary triage:
- severity: {$assessment['severity']}
- urgent: {$assessment['is_urgent']}
- recommended specialty: {$assessment['recommended_specialty']}

Suggested doctors:
{$doctorList}

Write a response that includes:
- empathic support
- initial assessment summary
- clear next step and specialty recommendation
- emergency warning only if needed
PROMPT;

        try {
            $response = Http::timeout(30)
                ->withToken($apiKey)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => $model,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => [
                                ['type' => 'input_text', 'text' => $systemPrompt],
                            ],
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'input_text', 'text' => $userPrompt],
                            ],
                        ],
                    ],
                    'temperature' => 0.4,
                    'max_output_tokens' => 450,
                ]);

            if (!$response->successful()) {
                Log::warning('Chat assistant API failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return $this->fallbackReply($assessment);
            }

            $data = $response->json();
            $reply = (string) ($data['output_text'] ?? '');

            if ($reply !== '') {
                return trim($reply);
            }

            $firstText = data_get($data, 'output.0.content.0.text');
            if (is_string($firstText) && trim($firstText) !== '') {
                return trim($firstText);
            }

            return $this->fallbackReply($assessment);
        } catch (\Throwable $e) {
            Log::error('Chat assistant exception', [
                'message' => $e->getMessage(),
            ]);

            return $this->fallbackReply($assessment);
        }
    }

    /**
     * Local fallback response if model is unavailable.
     */
    private function fallbackReply(array $assessment): string
    {
        if ($assessment['is_urgent']) {
            return 'أفهم أن وضعك صعب جدًا الآن. سلامتك أولًا: تواصل فورًا مع الطوارئ المحلية أو شخص موثوق قريب منك الآن، ولا تبقَ وحدك. بعد الأمان الفوري، يفضّل مراجعة طبيب ' . $assessment['recommended_specialty'] . ' بشكل عاجل.';
        }

        return 'شكرًا لمشاركتك. من وصفك، يبدو أن أفضل خطوة الآن هي تقييم أولي مع مختص ' . $assessment['recommended_specialty'] . '. إلى أن يتم الموعد: حاول تنظيم النوم، تقليل المنبهات، وتمارين تنفس هادئة 5 دقائق. إذا ساءت الأعراض بشكل مفاجئ، اطلب مساعدة طبية مباشرة.';
    }

    /**
     * Check whether text contains any keyword.
     */
    private function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($text, mb_strtolower($keyword, 'UTF-8'))) {
                return true;
            }
        }

        return false;
    }
}

