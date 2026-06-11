<template>
  <section class="space-y-12">
    <nav class="text-sm text-muted">
      <router-link to="/" class="hover:text-main">Accueil</router-link>
      <span class="mx-2">/</span>
      <span class="text-main">Gestion locative</span>
    </nav>

    <header class="space-y-6">
      <div class="section-label">Gestion locative</div>
      <h1 class="max-w-4xl text-4xl font-extrabold leading-tight text-main md:text-6xl">
        Gestion locative de biens immobiliers
      </h1>
      <p class="max-w-3xl text-lg leading-8 text-sub">
        Louer son bien demande du temps, de la methode et une attention constante.
        Immo Hub accompagne les proprietaires dans la gestion quotidienne de leurs logements,
        de la mise en location au suivi des occupants.
      </p>
    </header>

    <div class="overflow-hidden rounded-2xl border" style="border-color:var(--border); background:var(--bg-card)">
      <img
        src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1600&q=80"
        alt="Interieur lumineux pour la gestion locative"
        class="h-[280px] w-full object-cover md:h-[460px]"
      />
    </div>

    <section class="mx-auto max-w-4xl text-center">
      <div class="section-label mb-4">Louer sereinement</div>
      <h2 class="text-3xl font-extrabold leading-tight text-main md:text-4xl">
        Un accompagnement complet pour valoriser votre patrimoine
      </h2>
      <p class="mt-5 text-base leading-8 text-sub">
        Notre role est de vous aider a securiser la relation locative, presenter votre bien avec soin,
        suivre les demandes entrantes et simplifier les demarches. La gestion peut concerner une location
        longue duree comme une location saisonniere, selon votre objectif.
      </p>
    </section>

    <section class="grid gap-5 lg:grid-cols-3">
      <article v-for="service in services" :key="service.title" class="LI-card p-6">
        <div class="section-label mb-3">{{ service.label }}</div>
        <h3 class="text-2xl font-extrabold text-main">{{ service.title }}</h3>
        <ul class="mt-5 space-y-3 text-sm leading-6 text-sub">
          <li v-for="item in service.items" :key="item" class="flex gap-3">
            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
            <span>{{ item }}</span>
          </li>
        </ul>
      </article>
    </section>

    <section class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
      <div class="glass p-7 md:p-9">
        <div class="section-label mb-3">Immo Hub</div>
        <h2 class="text-3xl font-extrabold text-main">Une presence locale, un suivi centralise</h2>
        <p class="mt-4 text-sm leading-7 text-sub">
          La plateforme couvre sept pays avec deux villes principales par marche. Elle permet aux proprietaires,
          agences et gestionnaires de presenter leurs biens dans un cadre clair, avec une monnaie commune en FCFA
          et des parcours orientes location.
        </p>
        <div class="mt-7 grid gap-3 sm:grid-cols-2">
          <div v-for="metric in metrics" :key="metric.label" class="rounded-xl border p-4" style="border-color:var(--border); background:var(--border-light)">
            <div class="text-2xl font-extrabold text-main">{{ metric.value }}</div>
            <div class="mt-1 text-xs uppercase tracking-wider text-muted">{{ metric.label }}</div>
          </div>
        </div>
      </div>

      <div class="glass p-7 md:p-9">
        <div class="section-label mb-3">Deroulement</div>
        <div class="space-y-6">
          <div v-for="step in steps" :key="step.title" class="grid gap-4 sm:grid-cols-[3rem_1fr]">
            <div class="grid h-12 w-12 place-items-center rounded-lg bg-orange-500 text-base font-black text-white">
              {{ step.number }}
            </div>
            <div>
              <h3 class="text-lg font-extrabold text-main">{{ step.title }}</h3>
              <p class="mt-2 text-sm leading-7 text-sub">{{ step.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="overflow-hidden rounded-2xl border" style="border-color:var(--border); background:var(--bg-card)">
      <div class="grid lg:grid-cols-[1fr_0.82fr]">
        <div class="p-8 md:p-10">
          <div class="section-label mb-3">Contact proprietaire</div>
          <h2 class="text-3xl font-extrabold text-main">Vous souhaitez nous confier la gestion de votre bien ?</h2>
          <p class="mt-4 max-w-2xl text-sm leading-7 text-sub">
            Transmettez-nous la ville, le type de bien, le mode de location souhaite et vos disponibilites.
            Nous vous recontactons pour evaluer la mission et preparer la mise en location.
          </p>
          <div class="mt-7 flex flex-wrap gap-3">
            <router-link to="/contact" class="btn-primary">Contacter Immo Hub</router-link>
            <router-link :to="{ path: '/events', query: { offerType: 'Location', rentalMode: 'long-term' } }" class="btn-outline">
              Voir les locations longue duree
            </router-link>
          </div>
        </div>
        <img
          src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=900&q=80"
          alt="Facade residentielle geree par Immo Hub"
          class="h-full min-h-[280px] w-full object-cover"
        />
      </div>
    </section>
  </section>
</template>

<script setup>
const services = [
  {
    label: 'Gestion administrative',
    title: 'Un cadre clair pour louer',
    items: [
      'Constitution du dossier du bien et verification des informations utiles.',
      'Preparation de l annonce, des conditions de location et du suivi documentaire.',
      'Centralisation des demandes de visite et des echanges importants.',
    ],
  },
  {
    label: 'Gestion technique',
    title: 'Un bien suivi dans le temps',
    items: [
      'Mise a jour des disponibilites, photos, prix et informations du logement.',
      'Signalement des besoins d entretien ou d intervention apres retour locataire.',
      'Coordination entre proprietaire, agence et candidats locataires.',
    ],
  },
  {
    label: 'Gestion financiere',
    title: 'Une vision simple des loyers',
    items: [
      'Aide au positionnement du loyer selon le pays, la ville et le type de bien.',
      'Suivi des demandes et des revenus attendus en FCFA.',
      'Synthese claire pour faciliter les decisions du proprietaire.',
    ],
  },
]

const metrics = [
  { value: '7', label: 'pays couverts' },
  { value: '14', label: 'villes prioritaires' },
  { value: 'FCFA', label: 'monnaie commune' },
  { value: '24-48h', label: 'reponse moyenne' },
]

const steps = [
  {
    number: '1',
    title: 'Estimation et cadrage',
    text: 'Nous analysons les caracteristiques du bien, sa localisation, son etat, le type de location souhaite et les objectifs du proprietaire.',
  },
  {
    number: '2',
    title: 'Mise en location',
    text: 'Le bien est presente avec une annonce claire, un prix coherent et un parcours adapte a la location saisonniere ou longue duree.',
  },
  {
    number: '3',
    title: 'Suivi et reporting',
    text: 'Les demandes, visites, retours et informations importantes sont suivis pour conserver une vision simple de la gestion du bien.',
  },
]
</script>
