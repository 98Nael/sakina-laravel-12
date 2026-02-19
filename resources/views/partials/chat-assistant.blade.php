<style>
    #chat-assistant-root .chat-fab {
        position: relative;
        height: 64px;
        width: 64px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        color: #06202a;
        background: linear-gradient(135deg, #22d3ee, #14b8a6);
        border: 1px solid rgba(103, 232, 249, 0.6);
        box-shadow: 0 16px 38px rgba(6, 182, 212, 0.35);
        animation: chat-fab-float 2.4s ease-in-out infinite;
        transition: transform 160ms ease;
    }

    #chat-assistant-root .chat-fab:hover {
        transform: scale(1.06);
    }

    #chat-assistant-root .chat-fab::after {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 9999px;
        border: 1px solid rgba(34, 211, 238, 0.45);
        animation: chat-fab-ring 2.4s ease-out infinite;
        pointer-events: none;
    }

    @keyframes chat-fab-float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-6px);
        }
    }

    @keyframes chat-fab-ring {
        0% {
            opacity: 0.75;
            transform: scale(0.95);
        }
        100% {
            opacity: 0;
            transform: scale(1.25);
        }
    }
</style>

<div id="chat-assistant-root" class="fixed bottom-5 right-5 z-50">
    <button
        id="chat-assistant-toggle"
        type="button"
        class="chat-fab"
        aria-label="Open AI Chat Assistant"
        title="AI Chat Assistant"
    >
        AI
    </button>

    <div id="chat-assistant-panel" class="mt-3 hidden w-[min(92vw,390px)] overflow-hidden rounded-2xl border border-white/15 bg-slate-950/95 shadow-2xl backdrop-blur">
        <div class="border-b border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#0f766e)] px-4 py-3">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-cyan-200">Mental Support</p>
                    <p class="mt-1 text-sm font-bold text-white">Sakina AI Assistant</p>
                    <p class="mt-1 text-xs text-slate-300">Initial guidance only, not a final medical diagnosis.</p>
                </div>
                <button
                    id="chat-assistant-close"
                    type="button"
                    aria-label="Close chat"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-white/20 text-sm font-bold text-slate-200 transition hover:border-cyan-300/40 hover:text-white"
                >
                    X
                </button>
            </div>
        </div>

        <div id="chat-assistant-messages" class="max-h-[48vh] min-h-[240px] space-y-3 overflow-y-auto px-4 py-4 text-sm"></div>

        <div class="border-t border-white/10 px-4 py-3">
            <div class="mb-2 flex flex-wrap gap-2">
                <button type="button" data-quick-message="I feel severe anxiety and panic." class="rounded-full border border-white/20 px-2.5 py-1 text-xs text-slate-200 hover:border-cyan-300/40 hover:text-white">
                    Anxiety help
                </button>
                <button type="button" data-quick-message="I cannot sleep and my mood is very low." class="rounded-full border border-white/20 px-2.5 py-1 text-xs text-slate-200 hover:border-cyan-300/40 hover:text-white">
                    Sleep and mood
                </button>
                <button type="button" data-quick-message="Which specialist should I visit first?" class="rounded-full border border-white/20 px-2.5 py-1 text-xs text-slate-200 hover:border-cyan-300/40 hover:text-white">
                    Recommend doctor
                </button>
            </div>

            <form id="chat-assistant-form" class="flex items-end gap-2">
                <textarea
                    id="chat-assistant-input"
                    rows="2"
                    placeholder="Write your question..."
                    class="min-h-[44px] flex-1 resize-none rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20"
                ></textarea>
                <button
                    id="chat-assistant-send"
                    type="submit"
                    class="rounded-xl bg-cyan-500 px-3 py-2 text-sm font-semibold text-slate-950 transition hover:bg-cyan-400 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        const toggleButton = document.getElementById('chat-assistant-toggle');
        const panel = document.getElementById('chat-assistant-panel');
        const closeButton = document.getElementById('chat-assistant-close');
        const messagesBox = document.getElementById('chat-assistant-messages');
        const form = document.getElementById('chat-assistant-form');
        const input = document.getElementById('chat-assistant-input');
        const sendButton = document.getElementById('chat-assistant-send');
        const quickButtons = document.querySelectorAll('[data-quick-message]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        if (!toggleButton || !panel || !messagesBox || !form || !input || !sendButton) {
            return;
        }

        const state = {
            open: false,
            sending: false,
            history: [],
        };

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const scrollToBottom = () => {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        };

        const setOpen = (open) => {
            state.open = open;
            panel.classList.toggle('hidden', !open);
            if (open) {
                setTimeout(() => input.focus(), 50);
            }
        };

        const setSending = (sending) => {
            state.sending = sending;
            sendButton.disabled = sending;
            sendButton.textContent = sending ? 'Thinking...' : 'Send';
        };

        const appendMessage = (role, text) => {
            const bubble = document.createElement('div');
            bubble.className = role === 'user'
                ? 'ml-8 rounded-2xl rounded-br-md bg-cyan-500 px-3 py-2 text-sm text-slate-950'
                : 'mr-8 rounded-2xl rounded-bl-md border border-white/10 bg-slate-900 px-3 py-2 text-sm text-slate-100';
            bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
            messagesBox.appendChild(bubble);
            scrollToBottom();
        };

        const appendSystemNote = (text) => {
            const note = document.createElement('div');
            note.className = 'rounded-xl border border-amber-300/30 bg-amber-400/10 px-3 py-2 text-xs text-amber-100';
            note.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
            messagesBox.appendChild(note);
            scrollToBottom();
        };

        const renderDoctorSuggestions = (doctors) => {
            if (!Array.isArray(doctors) || !doctors.length) return;

            const list = doctors
                .map((doctor) => `- ${doctor.name} (${doctor.specialization || 'Specialist'})`)
                .join('\n');

            appendSystemNote(`Recommended doctors:\n${list}\n\nYou can continue from Get Started to create your patient account and book an appointment.`);
        };

        const sendMessage = async (message) => {
            const trimmed = (message || '').trim();
            if (!trimmed || state.sending) return;

            appendMessage('user', trimmed);
            state.history.push({ role: 'user', text: trimmed });
            state.history = state.history.slice(-12);
            input.value = '';
            setSending(true);

            try {
                const response = await fetch('/chat-assistant', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        message: trimmed,
                        history: state.history,
                    }),
                });

                if (!response.ok) {
                    appendSystemNote('Assistant is temporarily unavailable. Please try again in a moment.');
                    return;
                }

                const data = await response.json();
                const reply = (data.reply || 'I could not generate a response right now.').trim();
                appendMessage('assistant', reply);
                state.history.push({ role: 'assistant', text: reply });
                state.history = state.history.slice(-12);

                if (data.triage?.is_urgent) {
                    appendSystemNote('Urgent: if you feel in immediate danger, contact local emergency services now and speak to a trusted person nearby.');
                } else if (data.triage?.recommended_specialty) {
                    appendSystemNote(`Initial assessment: ${data.triage.notes}\nSuggested specialty: ${data.triage.recommended_specialty}`);
                }

                renderDoctorSuggestions(data.recommended_doctors);
            } catch (error) {
                appendSystemNote('Connection issue. Please check your internet and try again.');
            } finally {
                setSending(false);
            }
        };

        toggleButton.addEventListener('click', () => {
            setOpen(!state.open);
        });

        closeButton?.addEventListener('click', () => {
            setOpen(false);
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage(input.value);
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage(input.value);
            }
        });

        quickButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const quickMessage = button.getAttribute('data-quick-message') || '';
                if (!state.open) setOpen(true);
                sendMessage(quickMessage);
            });
        });

        appendMessage('assistant', 'Hello, I am Sakina AI assistant. I can provide initial emotional support, a first assessment, and suggest the right doctor specialty.');
    })();
</script>

