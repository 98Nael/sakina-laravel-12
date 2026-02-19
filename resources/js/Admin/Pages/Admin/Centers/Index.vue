<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-white/10 bg-[linear-gradient(135deg,#0f172a,#111827,#1f2937)] p-6 text-slate-100 shadow-2xl shadow-black/20">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.22em] text-amber-300">Centers Management</p>
          <h1 class="mt-2 text-2xl font-bold text-white">Mental Clinics and Health Centers</h1>
          <p class="mt-1 text-sm text-slate-300">Manage registered organizations, add coordinates, and publish all centers on Google Maps.</p>
        </div>
        <button
          type="button"
          class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2.5 text-sm font-semibold text-slate-900 transition hover:from-amber-400 hover:to-orange-400"
          @click="toggleCreateForm"
        >
          {{ showCreate ? 'Hide Add Form' : 'Add Center' }}
        </button>
      </div>

      <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
          <p class="text-xs text-slate-400">Total</p>
          <p class="mt-1 text-xl font-bold text-white">{{ stats.total }}</p>
        </div>
        <div class="rounded-xl border border-cyan-400/25 bg-cyan-500/10 p-3">
          <p class="text-xs text-cyan-200">Mental Clinics</p>
          <p class="mt-1 text-xl font-bold text-cyan-100">{{ stats.mental_clinics }}</p>
        </div>
        <div class="rounded-xl border border-indigo-400/25 bg-indigo-500/10 p-3">
          <p class="text-xs text-indigo-200">Health Centers</p>
          <p class="mt-1 text-xl font-bold text-indigo-100">{{ stats.health_centers }}</p>
        </div>
        <div class="rounded-xl border border-emerald-400/25 bg-emerald-500/10 p-3">
          <p class="text-xs text-emerald-200">Active</p>
          <p class="mt-1 text-xl font-bold text-emerald-100">{{ stats.active }}</p>
        </div>
      </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <h2 class="text-lg font-semibold text-slate-900">Google Map - All Centers</h2>
          <p class="text-sm text-slate-500">Pins displayed: {{ mapCenters.length }} centers with valid coordinates.</p>
        </div>
        <p class="text-xs text-slate-500">
          ملاحظة: افتح نموذج الإضافة أو التعديل ثم اضغط على أي نقطة في الخريطة لتعبئة خط العرض وخط الطول تلقائيا.
        </p>
      </div>

      <p v-if="mapError" class="mb-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        {{ mapError }}
      </p>

      <div ref="mapElement" class="h-[420px] w-full rounded-xl border border-slate-200 bg-slate-100"></div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Filters</h2>
      <form class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4" @submit.prevent="applyFilters">
        <input v-model="filterForm.search" type="text" placeholder="Search name, city, phone, email" class="rounded-lg border border-slate-300 px-3 py-2" />

        <select v-model="filterForm.type" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Types</option>
          <option value="mental_clinic">Mental Clinic</option>
          <option value="health_center">Health Center</option>
        </select>

        <select v-model="filterForm.status" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <select v-model="filterForm.sort" class="rounded-lg border border-slate-300 px-3 py-2">
          <option value="latest">Latest</option>
          <option value="oldest">Oldest</option>
          <option value="name_asc">Name A-Z</option>
          <option value="name_desc">Name Z-A</option>
        </select>

        <div class="flex gap-2 xl:col-span-4">
          <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Apply Filters</button>
          <button type="button" class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50" @click="resetFilters">Reset</button>
        </div>
      </form>
    </section>

    <section v-if="showCreate" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Create New Center</h2>
      <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submitCreate">
        <div>
          <input v-model="createForm.name" type="text" placeholder="Center name" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
        </div>
        <div>
          <select v-model="createForm.type" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            <option value="mental_clinic">Mental Clinic</option>
            <option value="health_center">Health Center</option>
          </select>
          <p v-if="createForm.errors.type" class="mt-1 text-xs text-red-600">{{ createForm.errors.type }}</p>
        </div>
        <div>
          <input v-model="createForm.city" type="text" placeholder="City" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.city" class="mt-1 text-xs text-red-600">{{ createForm.errors.city }}</p>
        </div>
        <div>
          <input v-model="createForm.phone" type="text" placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.phone" class="mt-1 text-xs text-red-600">{{ createForm.errors.phone }}</p>
        </div>
        <div>
          <input v-model="createForm.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.email" class="mt-1 text-xs text-red-600">{{ createForm.errors.email }}</p>
        </div>
        <div>
          <input v-model="createForm.address" type="text" placeholder="Address" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.address" class="mt-1 text-xs text-red-600">{{ createForm.errors.address }}</p>
        </div>
        <div>
          <input v-model="createForm.latitude" type="number" step="0.0000001" placeholder="Latitude (e.g. 24.7136)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.latitude" class="mt-1 text-xs text-red-600">{{ createForm.errors.latitude }}</p>
        </div>
        <div>
          <input v-model="createForm.longitude" type="number" step="0.0000001" placeholder="Longitude (e.g. 46.6753)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
          <p v-if="createForm.errors.longitude" class="mt-1 text-xs text-red-600">{{ createForm.errors.longitude }}</p>
        </div>
        <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 md:col-span-2">
          <input v-model="createForm.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500" />
          Active center
        </label>
        <div class="md:col-span-2">
          <button :disabled="createForm.processing" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
            {{ createForm.processing ? 'Saving...' : 'Create Center' }}
          </button>
        </div>
      </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-slate-900">Centers</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Name</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Type</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">City</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Contact</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Coordinates</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Status</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in centers.data" :key="item.id" class="border-t border-slate-100">
              <td class="px-4 py-3 text-slate-900">
                <p class="font-medium">{{ item.name }}</p>
                <p class="text-xs text-slate-500">{{ item.address || '-' }}</p>
              </td>
              <td class="px-4 py-3">
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                  {{ typeLabel(item.type) }}
                </span>
              </td>
              <td class="px-4 py-3 text-slate-700">{{ item.city || '-' }}</td>
              <td class="px-4 py-3 text-slate-700">
                <p>{{ item.phone || '-' }}</p>
                <p class="text-xs text-slate-500">{{ item.email || '-' }}</p>
              </td>
              <td class="px-4 py-3 text-slate-700">
                <template v-if="hasCoordinates(item)">
                  <p>{{ formatCoordinate(item.latitude) }}, {{ formatCoordinate(item.longitude) }}</p>
                  <a :href="googleMapsLink(item)" target="_blank" rel="noopener" class="text-xs font-medium text-blue-600 hover:text-blue-700">
                    Open in Google Maps
                  </a>
                </template>
                <span v-else class="text-slate-400">-</span>
              </td>
              <td class="px-4 py-3">
                <span class="rounded-full px-2.5 py-1 text-xs font-medium uppercase" :class="item.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'">
                  {{ item.is_active ? 'active' : 'inactive' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <button class="rounded-lg border border-blue-300 px-2.5 py-1 text-xs text-blue-700 hover:bg-blue-50" @click="openEdit(item)">
                    Edit
                  </button>
                  <button
                    class="rounded-lg border px-2.5 py-1 text-xs hover:bg-slate-50"
                    :class="item.is_active ? 'border-amber-300 text-amber-700' : 'border-emerald-300 text-emerald-700'"
                    @click="toggleStatus(item)"
                  >
                    {{ item.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                  <button class="rounded-lg border border-red-300 px-2.5 py-1 text-xs text-red-700 hover:bg-red-50" @click="destroyCenter(item)">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!centers.data.length">
              <td colspan="7" class="px-4 py-6 text-center text-slate-500">No centers found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="centers.links?.length" class="flex flex-wrap gap-2 border-t border-slate-200 px-6 py-4">
        <Link
          v-for="(link, idx) in centers.links"
          :key="idx"
          :href="link.url || '#'"
          :class="[
            'rounded-lg px-3 py-1.5 text-sm',
            link.active ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200',
            !link.url ? 'pointer-events-none opacity-50' : '',
          ]"
          v-html="link.label"
        />
      </div>
    </section>

    <div v-if="showEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeEdit">
      <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Edit Center</h3>
          <button class="text-slate-400 hover:text-slate-700" @click="closeEdit">X</button>
        </div>

        <form class="grid grid-cols-1 gap-4 md:grid-cols-2" @submit.prevent="submitEdit">
          <div>
            <input v-model="editForm.name" type="text" placeholder="Center name" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.name" class="mt-1 text-xs text-red-600">{{ editForm.errors.name }}</p>
          </div>
          <div>
            <select v-model="editForm.type" class="w-full rounded-lg border border-slate-300 px-3 py-2">
              <option value="mental_clinic">Mental Clinic</option>
              <option value="health_center">Health Center</option>
            </select>
            <p v-if="editForm.errors.type" class="mt-1 text-xs text-red-600">{{ editForm.errors.type }}</p>
          </div>
          <div>
            <input v-model="editForm.city" type="text" placeholder="City" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.city" class="mt-1 text-xs text-red-600">{{ editForm.errors.city }}</p>
          </div>
          <div>
            <input v-model="editForm.phone" type="text" placeholder="Phone" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.phone" class="mt-1 text-xs text-red-600">{{ editForm.errors.phone }}</p>
          </div>
          <div>
            <input v-model="editForm.email" type="email" placeholder="Email" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.email" class="mt-1 text-xs text-red-600">{{ editForm.errors.email }}</p>
          </div>
          <div>
            <input v-model="editForm.address" type="text" placeholder="Address" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.address" class="mt-1 text-xs text-red-600">{{ editForm.errors.address }}</p>
          </div>
          <div>
            <input v-model="editForm.latitude" type="number" step="0.0000001" placeholder="Latitude (e.g. 24.7136)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.latitude" class="mt-1 text-xs text-red-600">{{ editForm.errors.latitude }}</p>
          </div>
          <div>
            <input v-model="editForm.longitude" type="number" step="0.0000001" placeholder="Longitude (e.g. 46.6753)" class="w-full rounded-lg border border-slate-300 px-3 py-2" />
            <p v-if="editForm.errors.longitude" class="mt-1 text-xs text-red-600">{{ editForm.errors.longitude }}</p>
          </div>
          <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 md:col-span-2">
            <input v-model="editForm.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500" />
            Active center
          </label>
          <div class="flex gap-2 md:col-span-2">
            <button :disabled="editForm.processing" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
              Update
            </button>
            <button type="button" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-700" @click="closeEdit">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';

const GOOGLE_MAPS_API_KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || '';
const DEFAULT_MAP_CENTER = { lat: 24.7136, lng: 46.6753 };

const props = defineProps({
  centers: {
    type: Object,
    required: true,
  },
  mapCenters: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({
      total: 0,
      mental_clinics: 0,
      health_centers: 0,
      active: 0,
    }),
  },
});

const mapElement = ref(null);
const mapError = ref('');

const showCreate = ref(false);
const showEdit = ref(false);
const selectedCenter = ref(null);

let mapInstance = null;
let mapClickListener = null;
let markerInstances = [];
let selectionMarker = null;
let infoWindow = null;
let mapScriptPromise = null;

const filterForm = useForm({
  search: props.filters.search || '',
  type: props.filters.type || '',
  status: props.filters.status || '',
  sort: props.filters.sort || 'latest',
});

const createForm = useForm({
  name: '',
  type: 'mental_clinic',
  city: '',
  phone: '',
  email: '',
  address: '',
  latitude: '',
  longitude: '',
  is_active: true,
});

const editForm = useForm({
  name: '',
  type: 'mental_clinic',
  city: '',
  phone: '',
  email: '',
  address: '',
  latitude: '',
  longitude: '',
  is_active: true,
});

function normalizeCoordinateInput(value) {
  if (value === '' || value === null || value === undefined) return null;
  const number = Number(value);
  return Number.isFinite(number) ? number : value;
}

function toNumber(value) {
  if (value === null || value === undefined || value === '') return null;
  const number = Number(value);
  return Number.isFinite(number) ? number : null;
}

function hasCoordinates(item) {
  const latitude = toNumber(item?.latitude);
  const longitude = toNumber(item?.longitude);

  return latitude !== null && longitude !== null;
}

function formatCoordinate(value) {
  const number = toNumber(value);
  return number === null ? '-' : number.toFixed(6);
}

function googleMapsLink(item) {
  if (!hasCoordinates(item)) return '#';
  return `https://www.google.com/maps?q=${item.latitude},${item.longitude}`;
}

function typeLabel(type) {
  if (type === 'mental_clinic') return 'Mental Clinic';
  if (type === 'health_center') return 'Health Center';
  return type || '-';
}

function escapeHtml(value) {
  return String(value || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function clearMarkers() {
  markerInstances.forEach((marker) => marker.setMap(null));
  markerInstances = [];
}

function clearSelectionMarker() {
  if (selectionMarker) {
    selectionMarker.setMap(null);
  }
  selectionMarker = null;
}

function normalizedMapCenters() {
  return (props.mapCenters || [])
    .map((center) => {
      const latitude = toNumber(center.latitude);
      const longitude = toNumber(center.longitude);

      if (latitude === null || longitude === null) return null;
      if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) return null;

      return {
        ...center,
        latitude,
        longitude,
      };
    })
    .filter(Boolean);
}

function renderMarkers() {
  if (!mapInstance || !window.google?.maps) return;

  clearMarkers();
  const centers = normalizedMapCenters();

  if (!centers.length) {
    mapInstance.setCenter(DEFAULT_MAP_CENTER);
    mapInstance.setZoom(6);
    return;
  }

  const bounds = new window.google.maps.LatLngBounds();

  centers.forEach((center) => {
    const position = { lat: center.latitude, lng: center.longitude };
    const marker = new window.google.maps.Marker({
      map: mapInstance,
      position,
      title: center.name,
    });

    marker.addListener('click', () => {
      if (!infoWindow) {
        infoWindow = new window.google.maps.InfoWindow();
      }

      infoWindow.setContent(`
        <div style="min-width: 180px; font-family: Arial, sans-serif;">
          <p style="margin:0; font-weight:700; color:#0f172a;">${escapeHtml(center.name)}</p>
          <p style="margin:4px 0 0; color:#334155; font-size:12px;">${escapeHtml(typeLabel(center.type))}</p>
          <p style="margin:4px 0 0; color:#475569; font-size:12px;">${escapeHtml(center.city || center.address || 'No location details')}</p>
        </div>
      `);

      infoWindow.open({
        anchor: marker,
        map: mapInstance,
      });
    });

    markerInstances.push(marker);
    bounds.extend(position);
  });

  if (centers.length === 1) {
    mapInstance.setCenter({ lat: centers[0].latitude, lng: centers[0].longitude });
    mapInstance.setZoom(13);
  } else {
    mapInstance.fitBounds(bounds, 70);
  }
}

function setCoordinatesFromMapClick(latitude, longitude) {
  const latValue = latitude.toFixed(7);
  const lngValue = longitude.toFixed(7);

  if (showEdit.value) {
    editForm.latitude = latValue;
    editForm.longitude = lngValue;
    return true;
  }

  if (showCreate.value) {
    createForm.latitude = latValue;
    createForm.longitude = lngValue;
    return true;
  }

  return false;
}

function placeSelectionMarker(latitude, longitude) {
  if (!mapInstance || !window.google?.maps) return;

  const position = { lat: latitude, lng: longitude };

  if (!selectionMarker) {
    selectionMarker = new window.google.maps.Marker({
      map: mapInstance,
      position,
      draggable: true,
      title: 'Selected location',
      zIndex: 2000,
    });

    selectionMarker.addListener('dragend', (event) => {
      const dragLat = event?.latLng?.lat?.();
      const dragLng = event?.latLng?.lng?.();

      if (typeof dragLat !== 'number' || typeof dragLng !== 'number') return;
      setCoordinatesFromMapClick(dragLat, dragLng);
    });

    return;
  }

  selectionMarker.setMap(mapInstance);
  selectionMarker.setPosition(position);
}

function toggleCreateForm() {
  showCreate.value = !showCreate.value;

  if (!showCreate.value && !showEdit.value) {
    clearSelectionMarker();
  }
}

function loadGoogleMapsApi() {
  if (window.google?.maps) {
    return Promise.resolve(window.google.maps);
  }

  if (!GOOGLE_MAPS_API_KEY) {
    return Promise.reject(new Error('missing_api_key'));
  }

  if (mapScriptPromise) {
    return mapScriptPromise;
  }

  mapScriptPromise = new Promise((resolve, reject) => {
    const existing = document.querySelector('script[data-google-maps="admin-centers"]');
    if (existing) {
      if (window.google?.maps) {
        resolve(window.google.maps);
      } else {
        existing.addEventListener('load', () => resolve(window.google.maps), { once: true });
        existing.addEventListener('error', () => reject(new Error('script_load_failed')), { once: true });
      }
      return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(GOOGLE_MAPS_API_KEY)}&v=weekly`;
    script.async = true;
    script.defer = true;
    script.dataset.googleMaps = 'admin-centers';
    script.onload = () => {
      if (window.google?.maps) {
        resolve(window.google.maps);
      } else {
        reject(new Error('maps_namespace_missing'));
      }
    };
    script.onerror = () => reject(new Error('script_load_failed'));

    document.head.appendChild(script);
  });

  return mapScriptPromise;
}

async function initMap() {
  if (!mapElement.value) return;

  mapError.value = '';

  try {
    await loadGoogleMapsApi();

    mapInstance = new window.google.maps.Map(mapElement.value, {
      center: DEFAULT_MAP_CENTER,
      zoom: 6,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true,
    });

    mapClickListener = mapInstance.addListener('click', (event) => {
      const latitude = event?.latLng?.lat?.();
      const longitude = event?.latLng?.lng?.();

      if (typeof latitude !== 'number' || typeof longitude !== 'number') return;
      const didUpdate = setCoordinatesFromMapClick(latitude, longitude);
      if (didUpdate) {
        placeSelectionMarker(latitude, longitude);
      }
    });

    renderMarkers();
  } catch (error) {
    if (error instanceof Error && error.message === 'missing_api_key') {
      mapError.value = 'Google Maps API key is missing. Add VITE_GOOGLE_MAPS_API_KEY to .env then restart Vite.';
      return;
    }

    mapError.value = 'Google Maps failed to load. Verify API key permissions and internet access.';
  }
}

function submitCreate() {
  createForm
    .transform((data) => ({
      ...data,
      latitude: normalizeCoordinateInput(data.latitude),
      longitude: normalizeCoordinateInput(data.longitude),
    }))
    .post('/admin/centers', {
      preserveScroll: true,
      onSuccess: () => {
        createForm.reset();
        createForm.type = 'mental_clinic';
        createForm.is_active = true;
        createForm.latitude = '';
        createForm.longitude = '';
        showCreate.value = false;
        clearSelectionMarker();
      },
    });
}

function openEdit(item) {
  selectedCenter.value = item;
  editForm.reset();
  editForm.clearErrors();
  editForm.name = item.name || '';
  editForm.type = item.type || 'mental_clinic';
  editForm.city = item.city || '';
  editForm.phone = item.phone || '';
  editForm.email = item.email || '';
  editForm.address = item.address || '';
  editForm.latitude = item.latitude ?? '';
  editForm.longitude = item.longitude ?? '';
  editForm.is_active = !!item.is_active;
  showEdit.value = true;

  if (mapInstance && hasCoordinates(item)) {
    const latitude = Number(item.latitude);
    const longitude = Number(item.longitude);
    mapInstance.panTo({ lat: latitude, lng: longitude });
    mapInstance.setZoom(13);
    placeSelectionMarker(latitude, longitude);
  } else {
    clearSelectionMarker();
  }
}

function closeEdit() {
  showEdit.value = false;
  selectedCenter.value = null;

  if (!showCreate.value) {
    clearSelectionMarker();
  }
}

function submitEdit() {
  if (!selectedCenter.value) return;

  editForm
    .transform((data) => ({
      ...data,
      _method: 'put',
      latitude: normalizeCoordinateInput(data.latitude),
      longitude: normalizeCoordinateInput(data.longitude),
    }))
    .post(`/admin/centers/${selectedCenter.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        closeEdit();
      },
    });
}

function destroyCenter(item) {
  if (!confirm(`Delete center ${item.name}?`)) return;

  router.delete(`/admin/centers/${item.id}`, {
    preserveScroll: true,
  });
}

function toggleStatus(item) {
  router.patch(
    `/admin/centers/${item.id}/status`,
    {
      is_active: !item.is_active,
    },
    {
      preserveScroll: true,
    },
  );
}

function applyFilters() {
  router.get('/admin/centers', buildFilterParams(), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.search = '';
  filterForm.type = '';
  filterForm.status = '';
  filterForm.sort = 'latest';

  router.get('/admin/centers', {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  });
}

function buildFilterParams() {
  const params = {
    search: filterForm.search,
    type: filterForm.type,
    status: filterForm.status,
    sort: filterForm.sort,
  };

  return Object.fromEntries(Object.entries(params).filter(([, value]) => value !== '' && value !== null && value !== undefined));
}

watch(
  () => props.mapCenters,
  () => {
    renderMarkers();
  },
  { deep: true },
);

onMounted(() => {
  initMap();
});

onBeforeUnmount(() => {
  clearMarkers();
  clearSelectionMarker();

  if (mapClickListener && window.google?.maps?.event) {
    window.google.maps.event.removeListener(mapClickListener);
  }

  mapClickListener = null;
  infoWindow = null;
  mapInstance = null;
});
</script>
