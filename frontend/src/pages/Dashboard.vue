<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useEventsStore } from '../stores/events'
import { useAuthStore } from '../stores/auth'

const eventsStore = useEventsStore()
const userStore = useAuthStore()
const route = useRoute()

const q = ref('')
const country = ref('')
const city = ref('')
const propertyType = ref('')
const offerType = ref('')
const rentalMode = ref('')
const maxRent = ref('')
const sortBy = ref('recent')

const countries = ['Republique du Congo', 'Gabon', 'Cameroun', 'Senegal', "Cote d'Ivoire", 'Benin', 'Republique Centrafricaine']
const citiesByCountry = {
  'Republique du Congo': ['Brazzaville', 'Pointe-Noire'],
  Gabon: ['Libreville', 'Port-Gentil'],
  Cameroun: ['Douala', 'Yaounde'],
  Senegal: ['Dakar', 'Saly'],
  "Cote d'Ivoire": ['Abidjan', 'Yamoussoukro'],
  Benin: ['Cotonou', 'Porto-Novo'],
  'Republique Centrafricaine': ['Bangui', 'Bimbo'],
}
const cities = computed(() => country.value ? (citiesByCountry[country.value] || []) : Object.values(citiesByCountry).flat())
const propertyTypes = ['Appartement', 'Maison', 'Studio', 'Villa', 'Bureau', 'Terrain']
const offerTypes = ['Location', 'Vente']

const buyShowcase = [
  {
    type: 'Villa',
    city: 'Abidjan',
    country: "Cote d'Ivoire",
    price: '95 000 000 FCFA',
    image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Maison',
    city: 'Libreville',
    country: 'Gabon',
    price: '72 000 000 FCFA',
    image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Appartement',
    city: 'Dakar',
    country: 'Senegal',
    price: '48 000 000 FCFA',
    image: 'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Terrain',
    city: 'Pointe-Noire',
    country: 'Republique du Congo',
    price: '28 000 000 FCFA',
    image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=700&q=80',
  },
]

const rentShowcase = [
  {
    type: 'Appartement meuble',
    city: 'Brazzaville',
    country: 'Republique du Congo',
    price: '45 000 FCFA au total',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Studio',
    city: 'Cotonou',
    country: 'Benin',
    price: '36 000 FCFA au total',
    image: 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Villa',
    city: 'Saly',
    country: 'Senegal',
    price: '75 000 FCFA au total',
    image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Chambre',
    city: 'Douala',
    country: 'Cameroun',
    price: '25 000 FCFA au total',
    image: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=700&q=80',
  },
]

function syncFiltersFromQuery(query) {
  offerType.value = typeof query.offerType === 'string' ? query.offerType : ''
  rentalMode.value = typeof query.rentalMode === 'string' ? query.rentalMode : ''
}

onMounted(() => {
  syncFiltersFromQuery(route.query)
  eventsStore.fetchEvents()
})

watch(
  () => route.query,
  (query) => syncFiltersFromQuery(query),
  { deep: true }
)

const pageTitle = computed(() => {
  if (offerType.value === 'Vente') return 'Acheter'
  if (rentalMode.value === 'seasonal') return 'Locations saisonnieres'
  if (rentalMode.value === 'long-term') return 'Locations longue duree'
  if (offerType.value === 'Location') return 'Louer'
  return 'Offres immobilieres'
})

const showImageCarousel = computed(() => offerType.value === 'Vente' || offerType.value === 'Location')
const showcaseTitle = computed(() => offerType.value === 'Vente' ? 'Biens a acheter' : 'Logements en location')
const showcaseItems = computed(() => {
  const source = offerType.value === 'Vente' ? buyShowcase : rentShowcase
  return [...source, ...source]
})

function formatPrice(property) {
  const value = property?.monthlyRent
  if (!value) return 'Prix sur demande'
  const suffix = property?.offerType === 'Vente' ? '' : ' / mois'
  return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA' + suffix
}

function matchText(property) {
  return [
    property.title,
    property.description,
    property.location,
    property.country,
    property.city,
    property.district,
    property.propertyType,
    property.offerType
  ].filter(Boolean).join(' ').toLowerCase()
}

const filteredEvents = computed(() => {
  const search = q.value.trim().toLowerCase()
  let list = [...eventsStore.events]

  if (search) list = list.filter((property) => matchText(property).includes(search))
  if (country.value) list = list.filter((property) => property.country === country.value)
  if (city.value) list = list.filter((property) => property.city === city.value)
  if (propertyType.value) list = list.filter((property) => property.propertyType === propertyType.value)
  if (offerType.value) list = list.filter((property) => property.offerType === offerType.value)
  if (maxRent.value) list = list.filter((property) => Number(property.monthlyRent || 0) <= Number(maxRent.value))

  if (sortBy.value === 'rentAsc') list.sort((a, b) => Number(a.monthlyRent || 0) - Number(b.monthlyRent || 0))
  if (sortBy.value === 'rentDesc') list.sort((a, b) => Number(b.monthlyRent || 0) - Number(a.monthlyRent || 0))
  if (sortBy.value === 'surfaceDesc') list.sort((a, b) => Number(b.surfaceM2 || 0) - Number(a.surfaceM2 || 0))

  return list
})

async function handleDelete(id) {
  if (!confirm('Supprimer cette annonce ?')) return
  await eventsStore.deleteEvent(id)
}
</script>

<template>
  <div>
    <div class="mb-8">
      <div class="section-label mb-2">Catalogue</div>
      <h1 class="text-3xl font-extrabold text-main">{{ pageTitle }}</h1>
      <p class="mt-2 text-sm text-sub">Trouvez une location, une vente, un logement ou un local au Congo, au Gabon, au Cameroun, au Senegal, en Cote d'Ivoire, au Benin ou en Republique Centrafricaine.</p>
    </div>

    <section v-if="showImageCarousel" class="mb-10 space-y-5">
      <div class="flex items-center justify-between gap-4">
        <h2 class="text-2xl font-extrabold text-main">{{ showcaseTitle }}</h2>
        <router-link to="/events" class="btn-ghost text-sm">Voir tout</router-link>
      </div>

      <div class="rbnb-row-viewport">
        <div class="rbnb-row is-scrolling">
          <article v-for="(item, index) in showcaseItems" :key="`${item.type}-${item.city}-${index}`" class="rbnb-card">
            <div class="rbnb-image-wrap">
              <img :src="item.image" :alt="`${item.type} a ${item.city}`" class="rbnb-image" loading="lazy" />
              <div class="rbnb-favorite-badge">{{ item.type }}</div>
            </div>
            <div class="mt-3">
              <h3 class="text-lg font-extrabold text-main">{{ item.city }} · {{ item.country }}</h3>
              <p class="text-sm text-sub">{{ item.price }}</p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <div class="grid gap-3 mb-10 lg:grid-cols-[1.25fr_0.75fr_0.75fr_0.75fr_0.75fr_0.75fr_0.7fr]">
      <input v-model="q" placeholder="Rechercher par quartier, ville, type..." class="LI-input h-12" />
      <select v-model="offerType" class="LI-input h-12">
        <option value="">Toutes les offres</option>
        <option v-for="item in offerTypes" :key="item" :value="item">{{ item }}</option>
      </select>
      <select v-model="country" class="LI-input h-12" @change="city = ''">
        <option value="">Tous les pays</option>
        <option v-for="item in countries" :key="item" :value="item">{{ item }}</option>
      </select>
      <select v-model="city" class="LI-input h-12">
        <option value="">Toutes les villes</option>
        <option v-for="item in cities" :key="item" :value="item">{{ item }}</option>
      </select>
      <select v-model="propertyType" class="LI-input h-12">
        <option value="">Tous les types</option>
        <option v-for="item in propertyTypes" :key="item" :value="item">{{ item }}</option>
      </select>
      <input v-model="maxRent" type="number" min="0" placeholder="Budget/prix max" class="LI-input h-12" />
      <select v-model="sortBy" class="LI-input h-12">
        <option value="recent">Recent</option>
        <option value="rentAsc">Prix croissant</option>
        <option value="rentDesc">Prix decroissant</option>
        <option value="surfaceDesc">Surface</option>
      </select>
    </div>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <p class="text-sm text-muted">{{ filteredEvents.length }} annonce{{ filteredEvents.length > 1 ? 's' : '' }} trouvee{{ filteredEvents.length > 1 ? 's' : '' }}</p>
      <router-link v-if="userStore.isOrganizer" to="/events/create" class="btn-primary h-11 px-5 rounded-xl">
        Publier une annonce
      </router-link>
    </div>

    <div v-if="eventsStore.loading" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <div v-for="i in 6" :key="i" class="LI-card animate-pulse p-6">
        <div class="h-5 w-2/3 rounded" style="background:var(--border)"></div>
        <div class="mt-4 h-4 rounded" style="background:var(--border-light)"></div>
        <div class="mt-2 h-4 w-4/5 rounded" style="background:var(--border-light)"></div>
      </div>
    </div>

    <div v-else-if="eventsStore.error" class="glass p-8 text-center">
      <p class="text-red-400 font-semibold mb-4">{{ eventsStore.error }}</p>
      <button @click="eventsStore.fetchEvents()" class="btn-outline text-xs">Reessayer</button>
    </div>

    <div v-else-if="filteredEvents.length === 0" class="text-center py-24">
      <h3 class="text-xl font-bold text-main mb-2">Aucune annonce trouvee</h3>
      <p class="text-muted text-sm mb-6">Essayez une autre ville, un autre budget ou publiez la premiere annonce.</p>
      <router-link v-if="userStore.isOrganizer" to="/events/create" class="btn-primary text-xs px-5 py-2.5">Publier une annonce</router-link>
    </div>

    <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <article v-for="property in filteredEvents" :key="property.id" class="LI-card p-6 flex flex-col">
        <div class="flex items-center justify-between gap-3">
          <span class="badge-green">{{ property.offerType || 'Location' }}</span>
          <span class="badge-orange">{{ property.propertyType || 'Bien' }}</span>
          <span class="text-xs text-muted">{{ property.city }} · {{ property.country }}</span>
        </div>

        <router-link :to="`/events/${property.id}`" class="mt-4 text-xl font-extrabold text-main hover:text-orange-500 transition">
          {{ property.title }}
        </router-link>
        <p class="mt-3 text-sm leading-6 text-sub line-clamp-3">{{ property.description || 'Aucune description.' }}</p>

        <div class="mt-5 grid grid-cols-3 gap-3 text-sm">
          <div>
            <div class="text-xs text-muted">Surface</div>
            <div class="font-bold text-main">{{ property.surfaceM2 || '-' }} m2</div>
          </div>
          <div>
            <div class="text-xs text-muted">Chambres</div>
            <div class="font-bold text-main">{{ property.bedrooms ?? '-' }}</div>
          </div>
          <div>
            <div class="text-xs text-muted">Bains</div>
            <div class="font-bold text-main">{{ property.bathrooms ?? '-' }}</div>
          </div>
        </div>

        <div class="mt-5 text-sm text-muted">{{ property.district }} - {{ property.location }}</div>
        <div class="mt-2 text-2xl font-extrabold text-main">{{ formatPrice(property) }}</div>
        <div class="text-xs text-muted">{{ property.offerType === 'Vente' ? 'prix de vente' : 'loyer mensuel' }}</div>

        <div class="mt-6 pt-4 border-t LI-border flex items-center gap-2 mt-auto">
          <router-link :to="`/events/${property.id}`" class="btn-primary text-xs px-4 py-2 rounded-lg">
            Demander une visite
          </router-link>
          <div v-if="userStore.isOrganizer" class="flex items-center gap-1.5 ml-auto">
            <button @click="eventsStore.togglePublish(property.id)" class="btn-ghost text-xs px-3 py-2">
              {{ property.isPublished ? 'Depublier' : 'Publier' }}
            </button>
            <button @click="handleDelete(property.id)" class="btn-danger text-xs px-3 py-2">Supprimer</button>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>
