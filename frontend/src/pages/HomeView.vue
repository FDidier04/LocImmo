<script setup>
import { computed, onMounted } from 'vue'
import { useEventsStore } from '../stores/events'
import { useAuthStore } from '../stores/auth'

const eventsStore = useEventsStore()
const authStore = useAuthStore()

const cities = ['Brazzaville', 'Dolisie', 'Pointe-Noire', 'Nkayi']
const searchDestinations = ['Brazzaville', 'Pointe-Noire', 'Dolisie', 'Nkayi']
const popularStays = [
  {
    type: 'Appartement',
    city: 'Brazzaville',
    dates: '3-5 juil.',
    host: 'Particulier',
    price: 45000,
    rating: '4,98',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Studio',
    city: 'Pointe-Noire',
    dates: '12-14 août',
    host: 'Agence',
    price: 38000,
    rating: '4,93',
    image: 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Villa',
    city: 'Brazzaville',
    dates: '26-28 juin',
    host: 'Particulier',
    price: 95000,
    rating: '4,96',
    image: 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Chambre',
    city: 'Dolisie',
    dates: '8-10 sept.',
    host: 'Particulier',
    price: 25000,
    rating: '4,85',
    image: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Appartement',
    city: 'Nkayi',
    dates: '2-4 oct.',
    host: 'Agence',
    price: 32000,
    rating: '4,84',
    image: 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=700&q=80',
  },
  {
    type: 'Maison',
    city: 'Pointe-Noire',
    dates: '15-18 nov.',
    host: 'Particulier',
    price: 70000,
    rating: '4,91',
    image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=700&q=80',
  },
]

onMounted(() => {
  if (!eventsStore.events.length) {
    eventsStore.fetchEvents()
  }
})

const featuredProperties = computed(() => eventsStore.events.slice(0, 3))

function formatPrice(property) {
  const value = property?.monthlyRent
  if (!value) return 'Prix sur demande'
  const suffix = property?.offerType === 'Vente' ? '' : ' / mois'
  return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA' + suffix
}

function formatStayPrice(value) {
  return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA au total'
}
</script>

<template>
  <section class="space-y-10">
    <div class="glass p-8 md:p-10">
      <div class="section-label mb-3">Offres immobilieres au Congo</div>
      <h2 class="text-3xl md:text-4xl font-extrabold text-main">LocImmo connecte acheteurs, locataires, proprietaires et agences</h2>
      <p class="mt-4 max-w-3xl text-base leading-7 text-sub">
        Publiez et trouvez des offres immobilieres a Brazzaville, Dolisie, Pointe-Noire et Nkayi avec une API Symfony,
        une interface Vue moderne, l'authentification JWT et une base RGPD deja structuree.
      </p>

      <div class="mt-8 flex flex-wrap gap-3">
        <router-link to="/events" class="btn-primary">Voir les annonces</router-link>
        <router-link v-if="authStore.isOrganizer" to="/events/create" class="btn-outline">Publier une annonce</router-link>
        <router-link v-else-if="authStore.isAuthenticated" to="/dashboard" class="btn-outline">Ouvrir mon espace</router-link>
        <router-link v-else to="/login" class="btn-outline">Se connecter</router-link>
      </div>
    </div>

    <section class="rbnb-search-wrap">
      <div class="rbnb-search">
        <label class="rbnb-search-segment">
          <span>Destination</span>
          <select aria-label="Destination">
            <option value="">Rechercher une destination</option>
            <option v-for="destination in searchDestinations" :key="destination" :value="destination">{{ destination }}</option>
          </select>
        </label>
        <label class="rbnb-search-segment">
          <span>Dates</span>
          <input type="text" placeholder="Quand ?" aria-label="Dates" />
        </label>
        <label class="rbnb-search-segment">
          <span>Voyageurs</span>
          <input type="text" placeholder="Ajouter des voyageurs" aria-label="Voyageurs" />
        </label>
        <router-link to="/events" class="rbnb-search-button" aria-label="Rechercher">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
          </svg>
        </router-link>
      </div>
    </section>

    <section class="space-y-5">
      <div class="flex items-center justify-between gap-4">
        <h3 class="text-2xl md:text-3xl font-extrabold text-main">Logements populaires · République du Congo</h3>
        <div class="hidden items-center gap-2 md:flex">
          <button class="rbnb-arrow is-muted" aria-label="Precedent">‹</button>
          <button class="rbnb-arrow" aria-label="Suivant">›</button>
        </div>
      </div>

      <div class="rbnb-row">
        <article v-for="stay in popularStays" :key="`${stay.type}-${stay.city}-${stay.dates}`" class="rbnb-card">
          <div class="rbnb-image-wrap">
            <img :src="stay.image" :alt="`${stay.type} a ${stay.city}`" class="rbnb-image" loading="lazy" />
            <div class="rbnb-favorite-badge">Coup de cœur voyageurs</div>
            <button class="rbnb-heart" aria-label="Ajouter aux favoris">
              <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
            </button>
          </div>
          <div class="mt-3">
            <h4 class="text-lg font-extrabold text-main">{{ stay.type }} · {{ stay.city }}</h4>
            <p class="text-sm text-sub">{{ stay.dates }} · {{ stay.host }}</p>
            <p class="text-sm text-sub">{{ formatStayPrice(stay.price) }} · ★ {{ stay.rating }}</p>
          </div>
        </article>
      </div>
    </section>

    <section class="grid gap-4 md:grid-cols-4">
      <article v-for="city in cities" :key="city" class="LI-card p-5">
        <div class="section-label mb-2">Ville</div>
        <h3 class="text-xl font-extrabold text-main">{{ city }}</h3>
        <p class="mt-2 text-sm text-sub">Locations, ventes, appartements, maisons, studios et bureaux disponibles.</p>
      </article>
    </section>

    <section>
      <div class="mb-5 flex items-end justify-between gap-4">
        <div>
          <div class="section-label mb-2">Apercu public</div>
          <h3 class="text-2xl font-extrabold text-main">Annonces recentes</h3>
        </div>
        <router-link to="/events" class="btn-ghost">Voir tout</router-link>
      </div>

      <div v-if="eventsStore.loading" class="grid gap-5 md:grid-cols-3">
        <div v-for="i in 3" :key="i" class="LI-card animate-pulse p-6">
          <div class="h-5 w-28 rounded" style="background:var(--border)"></div>
          <div class="mt-4 h-4 rounded" style="background:var(--border-light)"></div>
          <div class="mt-2 h-4 w-2/3 rounded" style="background:var(--border-light)"></div>
        </div>
      </div>

      <div v-else class="grid gap-5 md:grid-cols-3">
        <article v-for="property in featuredProperties" :key="property.id" class="LI-card p-6">
          <div class="flex items-center justify-between gap-3">
            <span class="badge-green">{{ property.offerType || 'Location' }}</span>
            <span class="badge-orange">{{ property.propertyType || 'Bien' }}</span>
            <span class="text-xs text-muted">{{ property.city }}</span>
          </div>
          <h4 class="mt-4 text-xl font-extrabold text-main">{{ property.title }}</h4>
          <p class="mt-3 line-clamp-3 text-sm leading-6 text-sub">{{ property.description }}</p>
          <div class="mt-5 text-sm text-muted">{{ property.district }} - {{ property.location }}</div>
          <div class="mt-3 text-lg font-extrabold text-main">{{ formatPrice(property) }}</div>
          <router-link :to="`/events/${property.id}`" class="btn-outline mt-6">Voir le detail</router-link>
        </article>
      </div>
    </section>
  </section>
</template>
