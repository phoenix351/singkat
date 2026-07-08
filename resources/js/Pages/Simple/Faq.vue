<template>
  <Head title="FAQ dan Panduan" />

  <SimpleLayout>
    <!-- Hero Banner -->
    <div
      class="faq-hero rounded-2xl mb-8 px-8 py-10 flex flex-col md:flex-row items-center gap-6"
    >
      <div class="flex-1">
        <div class="flex items-center gap-3 mb-3">
          <div class="hero-icon-wrap">
            <i class="pi pi-question-circle text-2xl text-blue-400"></i>
          </div>
          <span
            class="text-blue-400 text-sm font-semibold tracking-widest uppercase"
            >Pusat Bantuan</span
          >
        </div>
        <h1 class="text-3xl font-bold text-white mb-2 leading-snug">
          FAQ &amp; Panduan Penggunaan
        </h1>
        <p class="text-slate-400 text-sm leading-relaxed max-w-xl">
          Temukan jawaban atas pertanyaan umum seputar SIMPLE, pahami alur
          pengajuan lembur, dan unduh panduan lengkap untuk membantu Anda
          menggunakan sistem ini dengan mudah.
        </p>
      </div>
      <div
        class="hidden md:flex items-center justify-center w-28 h-28 rounded-full bg-blue-500/10 border border-blue-500/20 flex-shrink-0"
      >
        <i class="pi pi-book text-5xl text-blue-400 opacity-80"></i>
      </div>
    </div>

    <!-- Flowchart Section -->
    <section class="mb-8">
      <div class="section-header mb-4">
        <div class="flex items-center gap-3">
          <div class="section-icon bg-orange-50 text-orange-500">
            <i class="pi pi-sitemap text-lg"></i>
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-800">
              Alur Pengajuan Lembur
            </h2>
            <p class="text-gray-500 text-sm">
              Diagram alur proses pengajuan lembur di SIMPLE
            </p>
          </div>
        </div>
      </div>
      <div
        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
      >
        <div class="flowchart-container p-4 md:p-6">
          <img
            src="/images/simple-assets/flowchart.png"
            alt="Alur Pengajuan Lembur"
            class="w-full h-auto object-contain rounded-xl max-h-[480px]"
          />
        </div>
        <!-- <div class="px-6 py-4 bg-orange-50/60 border-t border-orange-100 flex items-center gap-2">
          <i class="pi pi-info-circle text-orange-400 text-sm"></i>
          <span class="text-orange-700 text-xs">
            Alur di atas berlaku untuk pengajuan lembur biasa. Lembur jenis lain mungkin memiliki alur berbeda.
          </span>
        </div> -->
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="mb-8">
      <div class="section-header mb-4">
        <div class="flex items-center gap-3">
          <div class="section-icon bg-blue-50 text-blue-500">
            <i class="pi pi-comments text-lg"></i>
          </div>
          <div>
            <h2 class="text-lg font-bold text-gray-800">
              Pertanyaan yang Sering Diajukan
            </h2>
            <p class="text-gray-500 text-sm">
              Klik pertanyaan untuk melihat jawabannya
            </p>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-3">
        <div
          v-for="(item, index) in faqItems"
          :key="index"
          class="faq-card bg-white rounded-xl border-gray-100 shadow-sm overflow-hidden transition-all duration-300"
          :class="{ 'faq-card--open': openIndex === index }"
        >
          <!-- Question -->
          <button
            class="w-full flex items-start gap-4 px-6 py-4 text-left focus:outline-none group"
            @click="toggle(index)"
          >
            <span class="faq-number flex-shrink-0 mt-0.5">{{ index + 1 }}</span>
            <span
              class="flex-1 text-gray-800 font-medium text-sm leading-relaxed group-hover:text-blue-700 transition-colors"
            >
              {{ item.question }}
            </span>
            <i
              class="pi text-gray-400 text-sm flex-shrink-0 mt-1 transition-transform duration-300"
              :class="
                openIndex === index
                  ? 'pi-chevron-up text-blue-500'
                  : 'pi-chevron-down'
              "
            ></i>
          </button>

          <!-- Answer -->
          <div
            class="faq-answer overflow-hidden transition-all duration-300"
            :style="
              openIndex === index
                ? `max-height: ${answerHeights[index]}px; opacity: 1;`
                : 'max-height: 0; opacity: 0;'
            "
          >
            <div :ref="(el) => setAnswerRef(el, index)" class="px-6 pb-5">
              <div class="ml-10 pl-4 border-l-2 border-blue-200">
                <p class="text-gray-600 text-sm leading-relaxed">
                  {{ item.answer }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Download Guide Section -->
    <section>
      <div
        class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-8 flex flex-col md:flex-row items-center gap-6 border border-slate-700 shadow-lg"
      >
        <div
          class="flex-shrink-0 flex items-center justify-center w-20 h-20 rounded-2xl bg-blue-500/15 border border-blue-400/20"
        >
          <i class="pi pi-file-pdf text-4xl text-blue-400"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
          <h2 class="text-white text-xl font-bold mb-1">Panduan Lengkap</h2>
          <p class="text-slate-400 text-sm leading-relaxed">
            Panduan Aplikasi Simple bisa dicek pada link di samping.
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
          <a
            href="https://1drv.ms/f/c/244a95365c92fd5a/IgB8ZLTB3Q6BTZfSBpNx4cnzAdlNSTrqL3SfWeTAiyIRFVA?e=nBKFaM"
            class="btn-download-primary flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-200"
            target="_blank"
          >
            <i class="pi pi-eye"></i>
            Lihat Panduan
          </a>
        </div>
      </div>
    </section>
  </SimpleLayout>
</template>

<script setup>
import { ref, nextTick } from "vue";
import SimpleLayout from "@/Layouts/Simple/SimpleLayout.vue";
import { Head } from "@inertiajs/vue3";

// FAQ Data — dibaca dari public/document/simple-assets/faq-and-question.csv via Inertia props
const props = defineProps({
  faqItems: {
    type: Array,
    default: () => [],
  },
});

const openIndex = ref(null);
const answerRefs = ref([]);
const answerHeights = ref({});

const setAnswerRef = (el, index) => {
  if (el) {
    answerRefs.value[index] = el;
  }
};

const toggle = async (index) => {
  if (openIndex.value === index) {
    openIndex.value = null;
  } else {
    openIndex.value = index;
    await nextTick();
    if (answerRefs.value[index]) {
      answerHeights.value[index] = answerRefs.value[index].scrollHeight;
    }
  }
};
</script>

<style scoped>
/* Hero */
.faq-hero {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1e3a5f 100%);
  border: 1px solid rgba(59, 130, 246, 0.15);
}

.hero-icon-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.75rem;
  background: rgba(59, 130, 246, 0.12);
  border: 1px solid rgba(59, 130, 246, 0.2);
}

/* Section Header */
.section-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 0.75rem;
  flex-shrink: 0;
}

/* Flowchart */
.flowchart-container {
  background: #f8fafc;
}

/* FAQ Card */
.faq-card {
  transition: box-shadow 0.2s, border-color 0.2s;
}

.faq-card:hover {
  box-shadow: 0 4px 16px rgba(59, 130, 246, 0.08);
  border-color: #bfdbfe;
}

.faq-card--open {
  border-color: #93c5fd;
  box-shadow: 0 4px 20px rgba(59, 130, 246, 0.12);
}

.faq-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.6rem;
  height: 1.6rem;
  background: #eff6ff;
  color: #3b82f6;
  border-radius: 50%;
  font-size: 0.7rem;
  font-weight: 700;
  flex-shrink: 0;
  transition: background 0.2s, color 0.2s;
}

.faq-card--open .faq-number {
  background: #3b82f6;
  color: #fff;
}

.faq-answer {
  transition: max-height 0.35s ease, opacity 0.3s ease;
}

/* Download Buttons */
.btn-download-primary {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: #fff;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.35);
}

.btn-download-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.45);
  background: linear-gradient(135deg, #60a5fa, #3b82f6);
}

.btn-download-secondary {
  background: rgba(255, 255, 255, 0.06);
  color: #94a3b8;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-download-secondary:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  transform: translateY(-2px);
}
</style>