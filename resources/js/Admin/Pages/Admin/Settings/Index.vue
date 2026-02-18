<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Admin Settings</p>
      <h1 class="mt-2 text-2xl font-bold text-white">Social Media and Privacy Control</h1>
      <p class="mt-1 text-sm text-slate-300">Manage footer social links and privacy policy content shown on landing page.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Add New Social Link</h2>
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="submitSocialCreate">
        <div>
          <input v-model="socialForm.platform" type="text" placeholder="Platform (facebook)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="socialForm.errors.platform" class="mt-1 text-xs text-red-600">{{ socialForm.errors.platform }}</p>
        </div>
        <div>
          <input v-model="socialForm.label" type="text" placeholder="Label (Facebook)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="socialForm.errors.label" class="mt-1 text-xs text-red-600">{{ socialForm.errors.label }}</p>
        </div>
        <div class="xl:col-span-2">
          <input v-model="socialForm.url" type="url" placeholder="https://facebook.com/your-page" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="socialForm.errors.url" class="mt-1 text-xs text-red-600">{{ socialForm.errors.url }}</p>
        </div>
        <div class="flex items-center gap-2">
          <input v-model.number="socialForm.sort_order" type="number" min="0" class="w-24 rounded-lg border border-slate-300 px-3 py-2" />
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input v-model="socialForm.is_active" type="checkbox" class="h-4 w-4" />
            Active
          </label>
          <button :disabled="socialForm.processing" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Add
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Saved Social Links</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Platform</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Label</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">URL</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Order</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in socialLinks" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3 capitalize text-slate-900">{{ item.platform }}</td>
              <td class="px-4 py-3 text-slate-700">{{ item.label }}</td>
              <td class="px-4 py-3 text-slate-600">
                <a :href="item.url" target="_blank" class="text-cyan-700 hover:underline">{{ item.url }}</a>
              </td>
              <td class="px-4 py-3 text-slate-600">{{ item.sort_order }}</td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="item.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'">
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button
                    class="rounded-lg border px-2.5 py-1 text-xs"
                    :class="item.is_active ? 'border-amber-300 text-amber-700 hover:bg-amber-50' : 'border-emerald-300 text-emerald-700 hover:bg-emerald-50'"
                    @click="toggleSocialStatus(item)"
                  >
                    {{ item.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroySocial(item)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!socialLinks.length">
              <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                No records yet. Default social links will be shown on landing page.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Privacy Policy</h2>
      <p class="mb-4 text-sm text-slate-500">Add policy content with professional formatting-ready text block.</p>
      <form class="space-y-3" @submit.prevent="submitPolicyCreate">
        <input v-model="policyForm.title" type="text" placeholder="Title (e.g., Privacy Policy)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        <p v-if="policyForm.errors.title" class="text-xs text-red-600">{{ policyForm.errors.title }}</p>

        <textarea
          v-model="policyForm.content"
          rows="8"
          placeholder="Enter privacy policy text..."
          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6"
        />
        <p v-if="policyForm.errors.content" class="text-xs text-red-600">{{ policyForm.errors.content }}</p>

        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input v-model="policyForm.is_active" type="checkbox" class="h-4 w-4" />
          Set as active policy on landing page
        </label>

        <div>
          <button :disabled="policyForm.processing" class="rounded-xl bg-violet-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-800">
            Add Privacy Policy
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Policy Records</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Title</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Preview</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in privacyPolicies" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3 text-slate-900">{{ item.title }}</td>
              <td class="px-4 py-3 text-slate-600">{{ truncate(item.content, 130) }}</td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="item.is_active ? 'bg-violet-100 text-violet-700' : 'bg-slate-100 text-slate-700'">
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="openPolicyEdit(item)">
                    Edit
                  </button>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyPolicy(item)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!privacyPolicies.length">
              <td colspan="4" class="px-4 py-6 text-center text-slate-500">
                No privacy policy text added yet.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">About Page Content</h2>
      <p class="mb-4 text-sm text-slate-500">Control headline and body text for the public About page.</p>
      <form class="space-y-3" @submit.prevent="submitAboutCreate">
        <input v-model="aboutForm.subtitle" type="text" placeholder="Subtitle (About Platform)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        <p v-if="aboutForm.errors.subtitle" class="text-xs text-red-600">{{ aboutForm.errors.subtitle }}</p>

        <input v-model="aboutForm.title" type="text" placeholder="Main title" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
        <p v-if="aboutForm.errors.title" class="text-xs text-red-600">{{ aboutForm.errors.title }}</p>

        <textarea
          v-model="aboutForm.content"
          rows="8"
          placeholder="Enter about page content..."
          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6"
        />
        <p v-if="aboutForm.errors.content" class="text-xs text-red-600">{{ aboutForm.errors.content }}</p>

        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input v-model="aboutForm.is_active" type="checkbox" class="h-4 w-4" />
          Set as active about content
        </label>

        <div>
          <button :disabled="aboutForm.processing" class="rounded-xl bg-indigo-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-800">
            Add About Content
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">About Records</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Subtitle</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Title</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Preview</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in aboutContents" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3 text-slate-700">{{ item.subtitle || '-' }}</td>
              <td class="px-4 py-3 text-slate-900">{{ item.title }}</td>
              <td class="px-4 py-3 text-slate-600">{{ truncate(item.content, 120) }}</td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="item.is_active ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-700'">
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="openAboutEdit(item)">
                    Edit
                  </button>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyAbout(item)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!aboutContents.length">
              <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                No about content added yet.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <div v-if="showPolicyEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closePolicyEdit">
      <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Edit Privacy Policy</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closePolicyEdit">X</button>
        </div>

        <form class="space-y-3" @submit.prevent="submitPolicyEdit">
          <input v-model="policyEditForm.title" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="policyEditForm.errors.title" class="text-xs text-red-600">{{ policyEditForm.errors.title }}</p>

          <textarea v-model="policyEditForm.content" rows="10" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6" />
          <p v-if="policyEditForm.errors.content" class="text-xs text-red-600">{{ policyEditForm.errors.content }}</p>

          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input v-model="policyEditForm.is_active" type="checkbox" class="h-4 w-4" />
            Active policy
          </label>

          <div class="flex gap-2">
            <button :disabled="policyEditForm.processing" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
              Update
            </button>
            <button type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700" @click="closePolicyEdit">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showAboutEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeAboutEdit">
      <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Edit About Content</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closeAboutEdit">X</button>
        </div>

        <form class="space-y-3" @submit.prevent="submitAboutEdit">
          <input v-model="aboutEditForm.subtitle" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="aboutEditForm.errors.subtitle" class="text-xs text-red-600">{{ aboutEditForm.errors.subtitle }}</p>

          <input v-model="aboutEditForm.title" type="text" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="aboutEditForm.errors.title" class="text-xs text-red-600">{{ aboutEditForm.errors.title }}</p>

          <textarea v-model="aboutEditForm.content" rows="10" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm leading-6" />
          <p v-if="aboutEditForm.errors.content" class="text-xs text-red-600">{{ aboutEditForm.errors.content }}</p>

          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input v-model="aboutEditForm.is_active" type="checkbox" class="h-4 w-4" />
            Active about content
          </label>

          <div class="flex gap-2">
            <button :disabled="aboutEditForm.processing" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
              Update
            </button>
            <button type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700" @click="closeAboutEdit">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="text-lg font-semibold text-slate-900">Default Social Links</h2>
      <p class="mt-1 text-sm text-slate-500">These links are displayed automatically when there are no saved records.</p>
      <div class="mt-4 grid grid-cols-1 gap-2 md:grid-cols-2">
        <div v-for="(item, idx) in defaults" :key="`${item.platform}-${idx}`" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
          <span class="font-semibold text-slate-700">{{ item.label }}</span>
          <span class="mx-2 text-slate-400">-</span>
          <span class="text-slate-600">{{ item.url }}</span>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
  socialLinks: {
    type: Array,
    default: () => [],
  },
  defaults: {
    type: Array,
    default: () => [],
  },
  privacyPolicies: {
    type: Array,
    default: () => [],
  },
  aboutContents: {
    type: Array,
    default: () => [],
  },
});

const showPolicyEdit = ref(false);
const selectedPolicy = ref(null);
const showAboutEdit = ref(false);
const selectedAbout = ref(null);

const socialForm = useForm({
  platform: '',
  label: '',
  url: '',
  sort_order: 0,
  is_active: true,
});

const policyForm = useForm({
  title: '',
  content: '',
  is_active: true,
});

const policyEditForm = useForm({
  title: '',
  content: '',
  is_active: false,
});

const aboutForm = useForm({
  subtitle: '',
  title: '',
  content: '',
  is_active: true,
});

const aboutEditForm = useForm({
  subtitle: '',
  title: '',
  content: '',
  is_active: false,
});

function submitSocialCreate() {
  socialForm.post('/admin/settings', {
    preserveScroll: true,
    onSuccess: () => {
      socialForm.reset();
      socialForm.sort_order = 0;
      socialForm.is_active = true;
    },
  });
}

function toggleSocialStatus(item) {
  router.patch(`/admin/settings/social-links/${item.id}/status`, {
    is_active: item.is_active ? 0 : 1,
  }, {
    preserveScroll: true,
  });
}

function destroySocial(item) {
  if (!confirm(`Delete ${item.label}?`)) return;

  router.delete(`/admin/settings/social-links/${item.id}`, {
    preserveScroll: true,
  });
}

function submitPolicyCreate() {
  policyForm.post('/admin/settings/privacy-policies', {
    preserveScroll: true,
    onSuccess: () => {
      policyForm.reset();
      policyForm.is_active = true;
    },
  });
}

function openPolicyEdit(item) {
  selectedPolicy.value = item;
  policyEditForm.reset();
  policyEditForm.clearErrors();
  policyEditForm.title = item.title || '';
  policyEditForm.content = item.content || '';
  policyEditForm.is_active = !!item.is_active;
  showPolicyEdit.value = true;
}

function closePolicyEdit() {
  showPolicyEdit.value = false;
  selectedPolicy.value = null;
}

function submitPolicyEdit() {
  if (!selectedPolicy.value) return;

  policyEditForm
    .transform((data) => ({ ...data, _method: 'put' }))
    .post(`/admin/settings/privacy-policies/${selectedPolicy.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        closePolicyEdit();
      },
    });
}

function destroyPolicy(item) {
  if (!confirm(`Delete policy "${item.title}"?`)) return;

  router.delete(`/admin/settings/privacy-policies/${item.id}`, {
    preserveScroll: true,
  });
}

function submitAboutCreate() {
  aboutForm.post('/admin/settings/about-contents', {
    preserveScroll: true,
    onSuccess: () => {
      aboutForm.reset();
      aboutForm.is_active = true;
    },
  });
}

function openAboutEdit(item) {
  selectedAbout.value = item;
  aboutEditForm.reset();
  aboutEditForm.clearErrors();
  aboutEditForm.subtitle = item.subtitle || '';
  aboutEditForm.title = item.title || '';
  aboutEditForm.content = item.content || '';
  aboutEditForm.is_active = !!item.is_active;
  showAboutEdit.value = true;
}

function closeAboutEdit() {
  showAboutEdit.value = false;
  selectedAbout.value = null;
}

function submitAboutEdit() {
  if (!selectedAbout.value) return;

  aboutEditForm
    .transform((data) => ({ ...data, _method: 'put' }))
    .post(`/admin/settings/about-contents/${selectedAbout.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        closeAboutEdit();
      },
    });
}

function destroyAbout(item) {
  if (!confirm(`Delete about content "${item.title}"?`)) return;

  router.delete(`/admin/settings/about-contents/${item.id}`, {
    preserveScroll: true,
  });
}

function truncate(value, length) {
  if (!value) return '-';
  if (value.length <= length) return value;
  return `${value.slice(0, length)}...`;
}
</script>
