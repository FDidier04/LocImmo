<script setup>
import { computed, onMounted } from 'vue'
import { useEventsStore } from '../stores/events'
import { useAuthStore } from '../stores/auth'

const eventsStore = useEventsStore()
const authStore = useAuthStore()

const cities = ['Brazzaville', 'Dolisie', 'Pointe-Noire', 'Nkayi']

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
