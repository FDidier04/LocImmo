<script setup>
import { computed, reactive, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEventsStore } from '../stores/events'

const route = useRoute()
const router = useRouter()
const eventsStore = useEventsStore()

const countries = ['Republique du Congo', 'Gabon', 'Cameroun', 'Senegal']
const citiesByCountry = {
  'Republique du Congo': ['Brazzaville', 'Dolisie', 'Pointe-Noire', 'Nkayi'],
  Gabon: ['Libreville', 'Port-Gentil'],
  Cameroun: ['Douala', 'Yaounde'],
  Senegal: ['Dakar', 'Saly', 'Saint-Louis'],
}
const propertyTypes = ['Appartement', 'Maison', 'Studio', 'Villa', 'Bureau', 'Terrain']
const offerTypes = ['Location', 'Vente']

const form = reactive({
  title: '',
  description: '',
  eventDate: '',
  endDate: '',
  location: '',
  country: 'Republique du Congo',
  city: 'Brazzaville',
  district: '',
  propertyType: 'Appartement',
  offerType: 'Location',
  monthlyRent: 150000,
  surfaceM2: 60,
  bedrooms: 2,
  bathrooms: 1,
  imageUrl: '',
  maxParticipants: 10,
  isPublished: false,
})
const cities = computed(() => citiesByCountry[form.country] || citiesByCountry['Republique du Congo'])
const errors = ref({})
const loadingInitial = ref(false)

const isEditMode = computed(() => !!route.params.id)
const pageTitle = computed(() => isEditMode.value ? 'Modifier une annonce' : 'Publier une annonce')

function toDatetimeLocal(value) {
  if (!value) return ''
  const date = new Date(value)
  const pad = (part) => String(part).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

onMounted(async () => {
  if (!isEditMode.value) return

  loadingInitial.value = true

  try {
    const existing = eventsStore.events.find((event) => String(event.id) === String(route.params.id))
    const source = existing || (await eventsStore.fetchEventById(route.params.id))

    Object.assign(form, {
      title: source.title || '',
      description: source.description || '',
      eventDate: toDatetimeLocal(source.eventDate),
      endDate: toDatetimeLocal(source.endDate),
      location: source.location || '',
      country: source.country || 'Republique du Congo',
      city: source.city || 'Brazzaville',
      district: source.district || '',
      propertyType: source.propertyType || 'Appartement',
      offerType: source.offerType || 'Location',
      monthlyRent: Number(source.monthlyRent || 150000),
      surfaceM2: Number(source.surfaceM2 || 60),
      bedrooms: Number(source.bedrooms || 0),
      bathrooms: Number(source.bathrooms || 0),
      imageUrl: source.imageUrl || '',
      maxParticipants: Number(source.maxParticipants || 10),
      isPublished: !!source.isPublished,
    })
  } catch (err) {
    errors.value.form = err?.response?.data?.message || 'Impossible de charger cette annonce.'
  } finally {
    loadingInitial.value = false
  }
})

async function submit() {
  errors.value = {}

  try {
    const payload = {
      ...form,
      monthlyRent: Number(form.monthlyRent),
      surfaceM2: Number(form.surfaceM2),
      bedrooms: Number(form.bedrooms),
      bathrooms: Number(form.bathrooms),
      maxParticipants: Number(form.maxParticipants),
      eventDate: form.eventDate ? new Date(form.eventDate).toISOString() : new Date(Date.now() + 86400000).toISOString(),
      endDate: form.endDate ? new Date(form.endDate).toISOString() : '',
      imageUrl: form.imageUrl || null,
    }

    const saved = isEditMode.value
      ? await eventsStore.updateEvent(route.params.id, payload)
      : await eventsStore.createEvent(payload)

    router.push(isEditMode.value ? `/events/${saved.id}` : '/dashboard')
  } catch (err) {
    const e = err?.response?.data?.errors
    if (e) {
      errors.value = e
    } else {
      errors.value.form = err?.response?.data?.message || 'Une erreur est survenue.'
    }
  }
}
</script>

<template>
  <div class="max-w-3xl mx-auto py-8">
    <div class="mb-10">
      <div class="section-label mb-3">Annonce immobiliere</div>
      <h1 class="text-3xl font-extrabold text-main">{{ pageTitle }}</h1>
      <p class="text-sub text-sm mt-2">
        Renseignez les informations utiles pour publier une offre immobiliere au Congo, au Gabon, au Cameroun ou au Senegal.
      </p>
    </div>

    <div v-if="errors.form" class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm">
      {{ errors.form }}
    </div>

    <div v-if="loadingInitial" class="glass p-8 animate-pulse">
      <div class="h-5 w-48 rounded" style="background:var(--border)"></div>
      <div class="mt-4 h-10 rounded-xl" style="background:var(--border-light)"></div>
      <div class="mt-4 h-28 rounded-xl" style="background:var(--border-light)"></div>
    </div>

    <div v-else class="glass p-8 space-y-6">
      <div>
        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Titre *</label>
        <input v-model="form.title" class="LI-input" placeholder="Ex: Appartement meuble au centre-ville" />
        <p v-if="errors.title" class="text-xs text-red-500 mt-1">{{ errors.title }}</p>
      </div>

      <div>
        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Description *</label>
        <textarea v-model="form.description" rows="5" class="LI-input resize-none" placeholder="Decrivez le bien, les conditions de location et les points forts du quartier."></textarea>
        <p v-if="errors.description" class="text-xs text-red-500 mt-1">{{ errors.description }}</p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Pays *</label>
          <select v-model="form.country" class="LI-input" @change="form.city = cities[0]">
            <option v-for="item in countries" :key="item" :value="item">{{ item }}</option>
          </select>
          <p v-if="errors.country" class="text-xs text-red-500 mt-1">{{ errors.country }}</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Ville *</label>
          <select v-model="form.city" class="LI-input">
            <option v-for="item in cities" :key="item" :value="item">{{ item }}</option>
          </select>
          <p v-if="errors.city" class="text-xs text-red-500 mt-1">{{ errors.city }}</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Quartier *</label>
          <input v-model="form.district" class="LI-input" placeholder="Ex: Bacongo, Tié-Tié, Mongo Kamba" />
          <p v-if="errors.district" class="text-xs text-red-500 mt-1">{{ errors.district }}</p>
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Adresse ou repere *</label>
        <input v-model="form.location" class="LI-input" placeholder="Ex: Avenue principale, proche marche" />
        <p v-if="errors.location" class="text-xs text-red-500 mt-1">{{ errors.location }}</p>
      </div>

      <div class="grid sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Offre *</label>
          <select v-model="form.offerType" class="LI-input">
            <option v-for="item in offerTypes" :key="item" :value="item">{{ item }}</option>
          </select>
          <p v-if="errors.offerType" class="text-xs text-red-500 mt-1">{{ errors.offerType }}</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Type *</label>
          <select v-model="form.propertyType" class="LI-input">
            <option v-for="item in propertyTypes" :key="item" :value="item">{{ item }}</option>
          </select>
          <p v-if="errors.propertyType" class="text-xs text-red-500 mt-1">{{ errors.propertyType }}</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">
            {{ form.offerType === 'Vente' ? 'Prix de vente FCFA *' : 'Loyer mensuel FCFA *' }}
          </label>
          <input v-model="form.monthlyRent" type="number" min="1" class="LI-input" />
          <p v-if="errors.monthlyRent" class="text-xs text-red-500 mt-1">{{ errors.monthlyRent }}</p>
        </div>
      </div>

      <div class="grid sm:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Surface m2 *</label>
          <input v-model="form.surfaceM2" type="number" min="1" class="LI-input" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Chambres</label>
          <input v-model="form.bedrooms" type="number" min="0" class="LI-input" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Bains</label>
          <input v-model="form.bathrooms" type="number" min="0" class="LI-input" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Visites max</label>
          <input v-model="form.maxParticipants" type="number" min="1" class="LI-input" />
        </div>
      </div>

      <div>
        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Disponible a partir du</label>
        <input v-model="form.eventDate" type="datetime-local" class="LI-input" />
        <p v-if="errors.eventDate" class="text-xs text-red-500 mt-1">{{ errors.eventDate }}</p>
      </div>

      <div>
        <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Image URL</label>
        <input v-model="form.imageUrl" class="LI-input" placeholder="https://..." />
      </div>

      <label class="flex items-center gap-3 p-4 rounded-xl border" style="background:var(--border-light);border-color:var(--border)">
        <input v-model="form.isPublished" type="checkbox" class="rounded" />
        <span class="text-sm text-sub">
          <span class="font-semibold text-main">Publier immediatement</span>
          <span class="text-muted ml-1">- visible dans la liste publique.</span>
        </span>
      </label>

      <div class="flex gap-4 pt-2">
        <button type="button" @click="router.back()" class="btn-outline flex-1 py-3.5 rounded-xl">
          Annuler
        </button>
        <button @click="submit" :disabled="eventsStore.loading" class="btn-primary flex-1 py-3.5 rounded-xl">
          {{ eventsStore.loading ? 'Enregistrement...' : (isEditMode ? 'Mettre a jour' : 'Publier') }}
        </button>
      </div>
    </div>
  </div>
</template>
