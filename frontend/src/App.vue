<script setup>
import { computed, onMounted, ref, watch } from "vue"
import { useRoute } from "vue-router"
import { useAuthStore } from "./stores/auth"
import { useTheme } from "./composables/useTheme"
import CookieBanner from "./components/CookieBanner.vue"

const userStore = useAuthStore()
const route = useRoute()
const mobileOpen = ref(false)
const language = ref(localStorage.getItem("immo-hub-language") || "fr")
const { isDark, toggle } = useTheme()

const isHome = computed(() => route.path === "/")

const labels = {
  fr: {
    home: "Accueil",
    listings: "Annonces",
    buy: "ACHETER",
    rent: "LOUER",
    propertyManagement: "GESTION LOCATIVE",
    seasonalRentals: "LOCATIONS SAISONNIERES",
    longTermRentals: "LOCATIONS LONGUE DUREE",
    dashboard: "Dashboard",
    publish: "Publier",
    publishListing: "Publier une annonce",
    postListing: "Déposer une annonce",
    admin: "Admin",
    administration: "Administration",
    contact: "Contact",
    privacy: "Confidentialité",
    login: "Connexion",
    register: "S'inscrire",
    profile: "Mon profil",
    logout: "Déconnexion",
    lightMode: "Mode clair",
    darkMode: "Mode sombre",
    heroLabel: "Offres immobilières en Afrique francophone",
    heroTitleLine1: "Trouver un bien,",
    heroTitleLine2: "simplement.",
    heroCopy: "Immo Hub centralise les offres immobilières en Afrique francophone, avec une expérience moderne et sécurisée.",
    start: "Commencer gratuitement",
    rgpd: "Conforme RGPD",
    secure: "Sécurisé RS256",
    legalPrivacy: "Confidentialité",
    terms: "Conditions générales",
    howItWorks: "Fonctionnement du site",
    company: "Infos sur l'entreprise",
    languageName: "Français (FR)",
    currency: "FCFA",
  },
  en: {
    home: "Home",
    listings: "Listings",
    buy: "BUY",
    rent: "RENT",
    propertyManagement: "PROPERTY MANAGEMENT",
    seasonalRentals: "SEASONAL RENTALS",
    longTermRentals: "LONG-TERM RENTALS",
    dashboard: "Dashboard",
    publish: "Publish",
    publishListing: "Publish a listing",
    postListing: "Post a listing",
    admin: "Admin",
    administration: "Administration",
    contact: "Contact",
    privacy: "Privacy",
    login: "Log in",
    register: "Sign up",
    profile: "My profile",
    logout: "Log out",
    lightMode: "Light mode",
    darkMode: "Dark mode",
    heroLabel: "Real estate offers in Central and West Africa",
    heroTitleLine1: "Find a property,",
    heroTitleLine2: "simply.",
    heroCopy: "Immo Hub centralizes real estate offers in Congo, Gabon, Cameroon, Senegal, Cote d'Ivoire, Benin and the Central African Republic, with a modern and secure experience.",
    start: "Get started for free",
    rgpd: "GDPR compliant",
    secure: "RS256 secured",
    legalPrivacy: "Privacy",
    terms: "Terms",
    howItWorks: "How the site works",
    company: "Company info",
    languageName: "English (EN)",
    currency: "XAF",
  },
}

const t = computed(() => labels[language.value] || labels.fr)

watch(
  language,
  (value) => {
    localStorage.setItem("immo-hub-language", value)
    document.documentElement.lang = value
  },
  { immediate: true }
)

onMounted(async () => {
  if (userStore.token && !userStore.user) {
    await userStore.fetchMe()
  }
})
</script>

<template>
  <div class="min-h-screen" style="background:var(--bg-base);color:var(--text-1)">
    <header class="sticky top-0 z-30 border-b backdrop-blur-xl" style="background:var(--bg-nav);border-color:var(--border)">
      <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-6">
        <router-link to="/" class="flex shrink-0 items-center gap-3">
          <div class="grid h-8 w-8 place-items-center rounded-lg bg-orange-500 text-sm font-black text-white">IH</div>
          <span class="hidden text-base font-bold tracking-wide text-main sm:block" style="font-family:'Space Grotesk',sans-serif">
            IMMO<span class="text-orange-500"> HUB</span>
          </span>
        </router-link>

        <nav class="hidden items-center gap-6 md:flex">
          <router-link class="nav-link nav-tab" to="/">{{ t.home }}</router-link>
          <router-link class="nav-link nav-tab" :to="{ path: '/events', query: { offerType: 'Vente' } }">{{ t.buy }}</router-link>
          <div class="nav-dropdown">
            <router-link class="nav-link nav-tab nav-dropdown-trigger" :to="{ path: '/events', query: { offerType: 'Location' } }">
              <span>{{ t.rent }}</span>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="m6 9 6 6 6-6"/>
              </svg>
            </router-link>
            <div class="nav-dropdown-menu">
              <router-link :to="{ path: '/events', query: { offerType: 'Location', rentalMode: 'seasonal' } }">{{ t.seasonalRentals }}</router-link>
              <router-link :to="{ path: '/events', query: { offerType: 'Location', rentalMode: 'long-term' } }">{{ t.longTermRentals }}</router-link>
            </div>
          </div>
          <router-link class="nav-link nav-tab" to="/gestion-locative">{{ t.propertyManagement }}</router-link>
          <router-link v-if="userStore.isAuthenticated" class="nav-link" to="/dashboard">{{ t.dashboard }}</router-link>
          <router-link v-if="userStore.isOrganizer" class="nav-link" to="/events/create">{{ t.publish }}</router-link>
          <router-link v-if="userStore.isAdmin" class="nav-link" to="/admin">{{ t.admin }}</router-link>
          <router-link class="nav-link nav-tab" to="/contact">{{ t.contact }}</router-link>
          <router-link class="nav-link" to="/privacy">{{ t.privacy }}</router-link>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
          <select v-model="language" class="language-select" :aria-label="t.languageName">
            <option value="fr">Français</option>
            <option value="en">English</option>
          </select>

          <button @click="toggle" class="theme-toggle" :title="isDark ? t.lightMode : t.darkMode">
            <svg v-if="isDark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13H21M3 12H2m15.54-6.46l-.7.7M7.16 16.84l-.7.7M18.36 18.36l-.7-.7M6.34 6.34l-.7-.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
          </button>

          <template v-if="!userStore.isAuthenticated">
            <router-link to="/events/create" class="btn-primary px-8 py-2 text-xs whitespace-nowrap">{{ t.postListing }}</router-link>
            <router-link to="/login" class="btn-primary px-4 py-2 text-xs">{{ t.login }}</router-link>
          </template>
          <template v-else>
            <router-link to="/profile" class="btn-ghost text-xs">{{ userStore.fullName || t.profile }}</router-link>
            <button @click="userStore.logout" class="btn-outline px-4 py-2 text-xs">{{ t.logout }}</button>
          </template>
        </div>

        <div class="flex items-center gap-2 md:hidden">
          <select v-model="language" class="language-select compact" :aria-label="t.languageName">
            <option value="fr">FR</option>
            <option value="en">EN</option>
          </select>
          <button @click="toggle" class="theme-toggle">
            <svg v-if="isDark" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13H21M3 12H2m15.54-6.46l-.7.7M7.16 16.84l-.7.7M18.36 18.36l-.7-.7M6.34 6.34l-.7-.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
          </button>
          <button @click="mobileOpen = !mobileOpen" class="rounded-lg p-2 text-sub hover:text-main">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <Transition name="slide-up">
        <div v-if="mobileOpen" class="space-y-1 border-t px-6 py-4 md:hidden" style="border-color:var(--border);background:var(--bg-card)">
          <router-link @click="mobileOpen=false" class="block py-2 text-sm font-bold text-sub hover:text-main" to="/">{{ t.home }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 text-sm font-bold text-sub hover:text-main" :to="{ path: '/events', query: { offerType: 'Vente' } }">{{ t.buy }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 text-sm font-bold text-sub hover:text-main" :to="{ path: '/events', query: { offerType: 'Location' } }">{{ t.rent }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 pl-4 text-sm text-sub hover:text-main" :to="{ path: '/events', query: { offerType: 'Location', rentalMode: 'seasonal' } }">{{ t.seasonalRentals }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 pl-4 text-sm text-sub hover:text-main" :to="{ path: '/events', query: { offerType: 'Location', rentalMode: 'long-term' } }">{{ t.longTermRentals }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 text-sm font-bold text-sub hover:text-main" to="/gestion-locative">{{ t.propertyManagement }}</router-link>
          <router-link v-if="userStore.isAuthenticated" @click="mobileOpen=false" class="block py-2 text-sm text-sub hover:text-main" to="/dashboard">{{ t.dashboard }}</router-link>
          <router-link v-if="userStore.isOrganizer" @click="mobileOpen=false" class="block py-2 text-sm text-sub hover:text-main" to="/events/create">{{ t.publishListing }}</router-link>
          <router-link v-if="userStore.isAdmin" @click="mobileOpen=false" class="block py-2 text-sm text-sub hover:text-main" to="/admin">{{ t.administration }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 text-sm text-sub hover:text-main" to="/contact">{{ t.contact }}</router-link>
          <router-link @click="mobileOpen=false" class="block py-2 text-sm text-sub hover:text-main" to="/privacy">{{ t.privacy }}</router-link>
          <div class="flex flex-col gap-2 border-t pt-3" style="border-color:var(--border)">
            <template v-if="!userStore.isAuthenticated">
              <router-link @click="mobileOpen=false" to="/events/create" class="btn-primary justify-center whitespace-nowrap">{{ t.postListing }}</router-link>
              <router-link @click="mobileOpen=false" to="/login" class="btn-primary justify-center">{{ t.login }}</router-link>
            </template>
            <template v-else>
              <router-link @click="mobileOpen=false" to="/profile" class="btn-ghost justify-start">{{ t.profile }}</router-link>
              <button @click="userStore.logout(); mobileOpen=false" class="btn-outline justify-center">{{ t.logout }}</button>
            </template>
          </div>
        </div>
      </Transition>
    </header>

    <section v-if="isHome" class="relative overflow-hidden">
      <div class="bg-grid pointer-events-none absolute inset-0 opacity-100"></div>
      <div class="pointer-events-none absolute left-1/2 top-0 h-[400px] w-[800px] -translate-x-1/2 rounded-full bg-orange-500/10 blur-[120px]"></div>

      <div class="relative mx-auto max-w-7xl px-6 pb-20 pt-24">
        <div class="max-w-3xl">
          <div class="section-label mb-4">{{ t.heroLabel }}</div>
          <h1 class="text-5xl font-extrabold leading-[1.05] tracking-tight text-main sm:text-6xl lg:text-7xl">
            {{ t.heroTitleLine1 }}<br>
            <span class="gradient-text">{{ t.heroTitleLine2 }}</span>
          </h1>
          <p class="mt-6 max-w-xl text-lg leading-relaxed text-sub">{{ t.heroCopy }}</p>

          <div class="mt-10 flex flex-wrap gap-4">
            <router-link v-if="!userStore.isAuthenticated" to="/register" class="btn-primary rounded-2xl px-8 py-4 text-base">{{ t.start }}</router-link>
            <router-link v-if="!userStore.isAuthenticated" to="/login" class="btn-outline rounded-2xl px-8 py-4 text-base">{{ t.login }}</router-link>
            <router-link v-if="userStore.isOrganizer" to="/events/create" class="btn-primary rounded-2xl px-8 py-4 text-base">{{ t.publishListing }}</router-link>
          </div>

          <div class="mt-14 flex flex-wrap gap-8">
            <div>
              <div class="gradient-text text-3xl font-extrabold">100%</div>
              <div class="mt-1 text-xs uppercase tracking-wider text-muted">{{ t.rgpd }}</div>
            </div>
            <div class="border-l pl-8" style="border-color:var(--border)">
              <div class="text-3xl font-extrabold text-main">JWT</div>
              <div class="mt-1 text-xs uppercase tracking-wider text-muted">{{ t.secure }}</div>
            </div>
            <div class="border-l pl-8" style="border-color:var(--border)">
              <div class="text-3xl font-extrabold text-main">REST</div>
              <div class="mt-1 text-xs uppercase tracking-wider text-muted">API Symfony 6.4</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <main class="mx-auto max-w-7xl px-6 pb-20" :class="isHome ? 'pt-4' : 'pt-10'">
      <router-view v-slot="{ Component }">
        <Transition name="fade" mode="out-in">
          <component :is="Component" />
        </Transition>
      </router-view>
    </main>

    <footer class="footer-bar">
      <div class="mx-auto flex max-w-7xl flex-col gap-5 px-6 py-8 lg:flex-row lg:items-center lg:justify-between">
        <div class="footer-left">
          <span>© 2026 Immo Hub, Inc.</span>
          <span class="footer-dot">·</span>
          <router-link to="/privacy">{{ t.legalPrivacy }}</router-link>
          <span class="footer-dot">·</span>
          <a href="#">{{ t.terms }}</a>
          <span class="footer-dot">·</span>
          <a href="#">{{ t.howItWorks }}</a>
          <span class="footer-dot">·</span>
          <a href="#">{{ t.company }}</a>
        </div>

        <div class="footer-right">
          <label class="footer-language">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 100-18 9 9 0 000 18zM3.6 9h16.8M3.6 15h16.8M12 3c2.1 2.2 3.2 5.2 3.2 9S14.1 18.8 12 21M12 3C9.9 5.2 8.8 8.2 8.8 12s1.1 6.8 3.2 9"/>
            </svg>
            <select v-model="language" :aria-label="t.languageName">
              <option value="fr">Français (FR)</option>
              <option value="en">English (EN)</option>
            </select>
          </label>
          <span class="footer-currency">{{ t.currency }}</span>
          <a class="footer-social" href="#" aria-label="Facebook"><span aria-hidden="true">f</span></a>
          <a class="footer-social" href="#" aria-label="X"><span aria-hidden="true">𝕏</span></a>
          <a class="footer-social" href="#" aria-label="Instagram">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <rect width="16" height="16" x="4" y="4" rx="4" stroke-width="2"/>
              <circle cx="12" cy="12" r="3" stroke-width="2"/>
              <path stroke-linecap="round" stroke-width="2" d="M17.5 6.5h.01"/>
            </svg>
          </a>
        </div>
      </div>
    </footer>

    <CookieBanner />
  </div>
</template>
