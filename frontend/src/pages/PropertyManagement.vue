<template>
  <section class="space-y-10">
    <div class="overflow-hidden rounded-2xl border" style="border-color:var(--border); background:var(--bg-card)">
      <div class="grid lg:grid-cols-[0.92fr_1.08fr]">
        <div class="p-8 md:p-12">
          <div class="section-label mb-4">Gestion locative</div>
          <h1 class="text-4xl font-extrabold leading-tight text-main md:text-5xl">
            Confiez votre bien, gardez la maitrise.
          </h1>
          <p class="mt-5 max-w-2xl text-base leading-7 text-sub">
            Immo Hub accompagne les proprietaires dans la mise en location, le suivi administratif,
            la relation locataire et la valorisation de leurs biens en Afrique francophone.
          </p>
          <div class="mt-8 flex flex-wrap gap-3">
            <router-link to="/contact" class="btn-primary">Demander un accompagnement</router-link>
            <router-link :to="{ path: '/events', query: { offerType: 'Location' } }" class="btn-outline">Voir les locations</router-link>
          </div>
        </div>
        <div class="min-h-[320px]">
          <img
            src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1200&q=80"
            alt="Salon lumineux gere par Immo Hub"
            class="h-full min-h-[320px] w-full object-cover"
          />
        </div>
      </div>
    </div>

    <section class="grid gap-4 md:grid-cols-3">
      <article v-for="item in serviceBlocks" :key="item.title" class="LI-card p-6">
        <div class="section-label mb-3">{{ item.label }}</div>
        <h2 class="text-xl font-extrabold text-main">{{ item.title }}</h2>
        <p class="mt-3 text-sm leading-6 text-sub">{{ item.text }}</p>
      </article>
    </section>

    <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
      <div class="glass p-6 md:p-8">
        <div class="section-label mb-3">Notre methode</div>
        <h2 class="text-3xl font-extrabold text-main">Un suivi simple, transparent et documente</h2>
        <p class="mt-4 text-sm leading-6 text-sub">
          Nous centralisons les informations utiles du bien, les demandes de visite, les pieces de suivi
          et les echanges essentiels pour que chaque proprietaire puisse suivre son actif sans complexite.
        </p>
        <div class="mt-6 grid gap-3 sm:grid-cols-2">
          <div v-for="metric in metrics" :key="metric.label" class="rounded-xl border p-4" style="border-color:var(--border); background:var(--border-light)">
            <div class="text-2xl font-extrabold text-main">{{ metric.value }}</div>
            <div class="mt-1 text-xs uppercase tracking-wider text-muted">{{ metric.label }}</div>
          </div>
        </div>
      </div>

      <div class="glass p-6 md:p-8">
        <div class="section-label mb-3">Parcours proprietaire</div>
        <div class="space-y-5">
          <div v-for="step in steps" :key="step.title" class="flex gap-4">
            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-orange-500 text-sm font-black text-white">
              {{ step.number }}
            </div>
            <div>
              <h3 class="font-extrabold text-main">{{ step.title }}</h3>
              <p class="mt-1 text-sm leading-6 text-sub">{{ step.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="rounded-2xl border p-8 md:flex md:items-center md:justify-between md:gap-8" style="border-color:var(--border); background:var(--bg-card)">
      <div>
        <div class="section-label mb-3">Proprietaires et agences</div>
        <h2 class="text-2xl font-extrabold text-main">Vous souhaitez deleguer la gestion de votre bien ?</h2>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-sub">
          Envoyez-nous les informations du logement, la ville, le type de location souhaite et vos disponibilites.
          L'equipe Immo Hub vous recontacte pour cadrer la mission.
        </p>
      </div>
      <router-link to="/contact" class="btn-primary mt-6 shrink-0 md:mt-0">Contacter Immo Hub</router-link>
    </section>
  </section>
</template>

<script setup>
const serviceBlocks = [
  {
    label: 'Administratif',
    title: 'Dossier locataire et contrats',
    text: 'Qualification des demandes, collecte des informations utiles, suivi des documents et preparation des elements de location.',
  },
  {
    label: 'Technique',
    title: 'Suivi du bien et disponibilites',
    text: 'Mise a jour des annonces, coordination des visites, signalement des besoins d entretien et suivi des disponibilites.',
  },
  {
    label: 'Financier',
    title: 'Prix, loyers et reporting',
    text: 'Aide au positionnement du loyer, suivi des demandes, synthese des performances et visibilite sur les revenus attendus.',
  },
]

const metrics = [
  { value: '7', label: 'pays couverts' },
  { value: '2', label: 'villes par pays' },
  { value: 'FCFA', label: 'monnaie locale' },
  { value: '24-48h', label: 'delai de reponse' },
]

const steps = [
  {
    number: '1',
    title: 'Evaluation du bien',
    text: 'Nous collectons les informations essentielles : ville, quartier, type de bien, photos, prix souhaite et conditions.',
  },
  {
    number: '2',
    title: 'Publication et diffusion',
    text: 'Le logement est prepare pour la plateforme avec une presentation claire pour la location saisonniere ou longue duree.',
  },
  {
    number: '3',
    title: 'Suivi des demandes',
    text: 'Les demandes de visite, les profils interesses et les echanges importants sont centralises pour faciliter la decision.',
  },
]
</script>
