<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutContent;
use App\Models\PrivacyPolicy;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Show system settings page
     */
    public function index()
    {
        $socialLinks = SocialLink::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get([
                'id',
                'platform',
                'label',
                'url',
                'sort_order',
                'is_active',
            ]);

        $privacyPolicies = PrivacyPolicy::query()
            ->latest()
            ->get([
                'id',
                'title',
                'content',
                'is_active',
                'created_at',
            ]);

        $aboutContents = AboutContent::query()
            ->latest()
            ->get([
                'id',
                'title',
                'subtitle',
                'content',
                'is_active',
                'created_at',
            ]);

        return Inertia::render('Admin/Settings/Index', [
            'socialLinks' => $socialLinks,
            'defaults' => $this->defaultSocialLinks(),
            'privacyPolicies' => $privacyPolicies,
            'aboutContents' => $aboutContents,
        ]);
    }

    /**
     * Add a social media link
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'label' => 'required|string|max:100',
            'url' => 'required|url|max:2048',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        SocialLink::create([
            'platform' => strtolower(trim($validated['platform'])),
            'label' => trim($validated['label']),
            'url' => trim($validated['url']),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return back()->with('success', 'Social link added successfully.');
    }

    /**
     * Update active status for a social link.
     */
    public function updateSocialLinkStatus(Request $request, SocialLink $socialLink)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $socialLink->update([
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'Social link status updated.');
    }

    /**
     * Delete social media link.
     */
    public function destroySocialLink(SocialLink $socialLink)
    {
        $socialLink->delete();

        return back()->with('success', 'Social link deleted.');
    }

    /**
     * Add a privacy policy text block.
     */
    public function storePrivacyPolicy(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:30|max:20000',
            'is_active' => 'nullable|boolean',
        ]);

        $activate = (bool) ($validated['is_active'] ?? true);
        if ($activate) {
            PrivacyPolicy::query()->update(['is_active' => false]);
        }

        PrivacyPolicy::create([
            'title' => trim($validated['title']),
            'content' => trim($validated['content']),
            'is_active' => $activate,
        ]);

        return back()->with('success', 'Privacy policy added successfully.');
    }

    /**
     * Update privacy policy.
     */
    public function updatePrivacyPolicy(Request $request, PrivacyPolicy $privacyPolicy)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:30|max:20000',
            'is_active' => 'required|boolean',
        ]);

        if ($validated['is_active']) {
            PrivacyPolicy::query()
                ->whereKeyNot($privacyPolicy->id)
                ->update(['is_active' => false]);
        }

        $privacyPolicy->update([
            'title' => trim($validated['title']),
            'content' => trim($validated['content']),
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'Privacy policy updated successfully.');
    }

    /**
     * Delete privacy policy.
     */
    public function destroyPrivacyPolicy(PrivacyPolicy $privacyPolicy)
    {
        $privacyPolicy->delete();

        return back()->with('success', 'Privacy policy deleted.');
    }

    /**
     * Add about page content block.
     */
    public function storeAboutContent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'required|string|min:30|max:20000',
            'is_active' => 'nullable|boolean',
        ]);

        $activate = (bool) ($validated['is_active'] ?? true);
        if ($activate) {
            AboutContent::query()->update(['is_active' => false]);
        }

        AboutContent::create([
            'title' => trim($validated['title']),
            'subtitle' => isset($validated['subtitle']) ? trim($validated['subtitle']) : null,
            'content' => trim($validated['content']),
            'is_active' => $activate,
        ]);

        return back()->with('success', 'About content added successfully.');
    }

    /**
     * Update about page content block.
     */
    public function updateAboutContent(Request $request, AboutContent $aboutContent)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'required|string|min:30|max:20000',
            'is_active' => 'required|boolean',
        ]);

        if ($validated['is_active']) {
            AboutContent::query()
                ->whereKeyNot($aboutContent->id)
                ->update(['is_active' => false]);
        }

        $aboutContent->update([
            'title' => trim($validated['title']),
            'subtitle' => isset($validated['subtitle']) ? trim($validated['subtitle']) : null,
            'content' => trim($validated['content']),
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'About content updated successfully.');
    }

    /**
     * Delete about page content block.
     */
    public function destroyAboutContent(AboutContent $aboutContent)
    {
        $aboutContent->delete();

        return back()->with('success', 'About content deleted.');
    }

    /**
     * Default social links used when no DB records exist.
     */
    private function defaultSocialLinks(): array
    {
        return [
            [
                'platform' => 'facebook',
                'label' => 'Facebook',
                'url' => 'https://facebook.com/',
            ],
            [
                'platform' => 'instagram',
                'label' => 'Instagram',
                'url' => 'https://instagram.com/',
            ],
            [
                'platform' => 'x',
                'label' => 'X (Twitter)',
                'url' => 'https://x.com/',
            ],
            [
                'platform' => 'linkedin',
                'label' => 'LinkedIn',
                'url' => 'https://linkedin.com/',
            ],
            [
                'platform' => 'youtube',
                'label' => 'YouTube',
                'url' => 'https://youtube.com/',
            ],
            [
                'platform' => 'tiktok',
                'label' => 'TikTok',
                'url' => 'https://tiktok.com/',
            ],
        ];
    }
}
