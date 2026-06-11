<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { api } from '../services/api'
import { useEventsStore } from '../stores/events'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const eventsStore = useEventsStore()
const authStore = useAuthStore()

const property = ref(null)
const loading = ref(true)
const error = ref('')
const feedback = ref('')

onMounted(async () => {
  loading.value = true
  error.value = ''

  try {
    const res = await api.get(`/properties/${route.params.id}`)
    property.value = res.data
  } catch (err) {
    error.value = err?.response?.data?.message || 'Impossible de charger cette annonce.'
  } finally {
    loading.value = false
  }
})

const mapEmbedUrl = computed(() => {
  const location = [property.value?.location, property.value?.district, property.value?.city, property.value?.country].filter(Boolean).join(', ')
  if (!location.trim()) return ''

  return `https://maps.google.com/maps?q=${encodeURIComponent(location)}&z=15&output=embed`
})

const mapSearchUrl = computed(() => {
  const location = [property.value?.location, property.value?.district, property.value?.city, property.value?.country].filter(Boolean).join(', ')
  if (!location.trim()) return ''

  return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location)}`
})

function formatPrice(property) {
  const value = property?.monthlyRent
  if (!value) return 'Prix sur demande'
  const suffix = property?.offerType === 'Vente' ? '' : ' / mois'
  return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA' + suffix
}

function formatDate(value) {
  if (!value) return 'A convenir'
  return new Intl.DateTimeFormat('fr-FR', {
    dateStyle: 'long',
    timeStyle: 'short',
  }).format(new Date(value))
}

async function requestVisit() {
  feedback.value = ''

  try {
    await eventsStore.registerToEvent(route.params.id)
    feedback.value = 'Demande de visite enregistree avec succes.'
  } catch (err) {
    feedback.value = err?.response?.data?.message || 'Impossible d enregistrer votre demande.'
  }
}
</script>

<template>
  <section class="space-y-8">
    <div v-if="loading" class="glass p-10">
      <div class="h-6 w-48 animate-pulse rounded" style="background:var(--border)"></div>
      <div class="mt-4 h-4 w-2/3 animate-pulse rounded" style="background:var(--border-light)"></div>
    </div>

    <div v-else-if="error" class="glass p-10 text-center">
      <h1 class="text-2xl font-extrabold text-main">Annonce introuvable</h1>
      <p class="mt-3 text-sm text-red-500">{{ error }}</p>
      <router-link to="/events" class="btn-primary mt-6">Retour aux annonces</router-link>
    </div>

    <template v-else-if="property">
      <div class="glass p-8 md:p-10">
        <div class="flex flex-wrap items-center gap-3">
          <span class="badge-green">{{ property.offerType || 'Location' }}</span>
          <span class="badge-purple">{{ property.propertyType || 'Bien' }}</span>
          <span class="badge-orange" v-if="!property.isPublished">brouillon</span>
          <span class="text-sm text-muted">{{ property.city }} - {{ property.country }} - {{ property.district }}</span>
        </div>

        <h1 class="mt-4 text-3xl md:text-4xl font-extrabold text-main">{{ property.title }}</h1>
        <p class="mt-4 max-w-3xl text-base leading-7 text-sub">{{ property.description }}</p>
        <div class="mt-6 text-3xl font-extrabold text-main">{{ formatPrice(property) }}</div>

        <div class="mt-8 grid gap-4 md:grid-cols-4">
          <div class="rounded-2xl border p-4" style="border-color:var(--border); background:var(--bg-card)">
            <div class="text-xs uppercase tracking-[0.18em] text-muted">Surface</div>
            <div class="mt-2 text-sm font-semibold text-main">{{ property.surfaceM2 }} m2</div>
          </div>
          <div class="rounded-2xl border p-4" style="border-color:var(--border); background:var(--bg-card)">
            <div class="text-xs uppercase tracking-[0.18em] text-muted">Chambres</div>
            <div class="mt-2 text-sm font-semibold text-main">{{ property.bedrooms }}</div>
          </div>
          <div class="rounded-2xl border p-4" style="border-color:var(--border); background:var(--bg-card)">
            <div class="text-xs uppercase tracking-[0.18em] text-muted">Salles d'eau</div>
            <div class="mt-2 text-sm font-semibold text-main">{{ property.bathrooms }}</div>
          </div>
          <div class="rounded-2xl border p-4" style="border-color:var(--border); background:var(--bg-card)">
            <div class="text-xs uppercase tracking-[0.18em] text-muted">Disponible</div>
            <div class="mt-2 text-sm font-semibold text-main">{{ formatDate(property.eventDate) }}</div>
          </div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="glass p-6">
          <div class="section-label mb-2">Adresse</div>
          <h2 class="text-2xl font-extrabold text-main">{{ property.location }}</h2>
          <p class="mt-3 text-sm leading-6 text-sub">
            {{ property.district }}, {{ property.city }}, {{ property.country }}. Les informations detaillees de visite sont confirmees apres votre demande.
          </p>
          <div class="mt-5 text-sm text-sub">
            Contact : {{ property.organizer?.firstName }} {{ property.organizer?.lastName }}
          </div>
        </section>

        <section class="glass p-6">
          <div class="section-label mb-2">Action</div>
          <h2 class="text-2xl font-extrabold text-main">Demander une visite</h2>
          <p class="mt-3 text-sm leading-6 text-sub">
            Connectez-vous pour envoyer une demande de visite au bailleur ou a l'agence responsable de cette annonce.
          </p>
          <div v-if="feedback" class="mt-4 rounded-xl border px-4 py-3 text-sm"
            :style="feedback.includes('succes') ? 'background:rgba(16,185,129,0.08); border-color:rgba(16,185,129,0.2); color:#059669' : 'background:rgba(239,68,68,0.08); border-color:rgba(239,68,68,0.2); color:#dc2626'">
            {{ feedback }}
          </div>
          <button v-if="authStore.isAuthenticated" class="btn-primary mt-5" @click="requestVisit">Demander une visite</button>
          <router-link v-else to="/login" class="btn-primary mt-5">Se connecter</router-link>
        </section>
      </div>

      <section class="glass p-6 md:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <div class="section-label mb-2">Localisation</div>
            <h2 class="text-2xl font-extrabold text-main">Voir le quartier sur la carte</h2>
          </div>
          <a v-if="mapSearchUrl" :href="mapSearchUrl" target="_blank" rel="noreferrer" class="btn-ghost text-sm">
            Ouvrir dans Google Maps
          </a>
        </div>

        <div class="mt-6 overflow-hidden rounded-3xl border" style="border-color:var(--border); background:var(--bg-card)">
          <iframe
            v-if="mapEmbedUrl"
            :src="mapEmbedUrl"
            title="Carte de localisation de l'annonce"
            class="h-[360px] w-full border-0"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          />
        </div>
      </section>
    </template>
  </section>
</template>
