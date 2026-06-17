<template>
  <div class="min-h-screen bg-gray-50 flex overflow-hidden">
    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300">
      <header
        class="sticky top-0 z-30 border-b backdrop-blur-md transition-colors bg-white/80 dark:bg-slate-900/80 border-slate-200 dark:border-slate-800"
      >
        <div
          class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between"
        >
          <div class="flex items-center gap-3">
            <div
              class="w-10 h-10 flex items-center justify-center bg-white dark:bg-slate-800 rounded-xl p-1 shadow-md border border-slate-200 dark:border-slate-700"
            >
              <img src="/icon.png" alt="Logo BPS" class="w-8 h-8 object-contain" />
            </div>
            <div>
              <h1
                class="text-sm sm:text-base font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2"
              >
                Dashboard Sensus Ekonomi 2026
              </h1>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Provinsi Sulawesi Utara
              </p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <Button
              icon="pi pi-upload"
              size="small"
              class="rounded-[12px]"
              severity="secondary"
              label="Upload Data"
            />
            <nav
              class="flex items-center gap-1 border border-slate-200 dark:border-slate-800 rounded-[12px] p-1 bg-slate-50/50 dark:bg-slate-950/50"
            >
              <a
                href="/"
                class="px-3 py-1.5 rounded-[9px] text-xs font-bold transition-all bg-orange-500 text-white shadow-sm"
              >
                Dashboard
              </a>
              <a
                href="/tabulasi"
                class="px-3 py-1.5 rounded-[9px] text-xs font-medium transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200"
              >
                Tabulasi
              </a>
            </nav>

            <button
              @click="isDarkMode = !isDarkMode"
              class="p-2.5 rounded-[9px] border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors"
              title="Ganti Tema"
            >
              <Sun v-if="isDarkMode" class="w-4 h-4 text-orange-400" />
              <Moon v-else class="w-4 h-4 text-slate-700" />
            </button>

            <button
              @click="fetchData"
              :disabled="loading"
              class="p-2.5 rounded-[9px] border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors disabled:opacity-50"
              title="Segarkan Data"
            >
              <RefreshCw
                :class="['w-4 h-4', { 'animate-spin text-orange-500': loading }]"
              />
            </button>
          </div>
        </div>
      </header>
    </div>
  </div>
</template>
