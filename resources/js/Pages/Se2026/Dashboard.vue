<template>
  <Head title="Dashboard" />
  <SpinnerBorder v-if="thisTriggerSpinner" />
  <SeLayout>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
      <!-- TOTAL TARGET PRELIST -->
      <div class="stat-card">
        <span class="stat-label">Total Assignment Petugas</span>
        <span class="stat-value text-slate-800">{{ formatNumber(total) }}</span>
        <!-- <div class="stat-footer text-sm !justify-start">
          Total dihitung dari assignment petugas
        </div> -->
      </div>

      <!-- TOTAL REALISASI -->
      <div class="stat-card">
        <span class="stat-label">Realisasi Pendataan</span>
        <span class="stat-value text-emerald-600">{{ formatNumber(realisasi) }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-emerald-500"
            :style="{ width: pctRealisasi + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-emerald-600">{{ pctRealisasi }}%</span>
        </div>
        <span class="stat-note">*Selain status OPEN dan DRAFT</span>
      </div>

      <!-- REALISASI VIA FASIH SM -->
      <!-- <div class="stat-card">
        <span class="stat-label">Realisasi via FASIH SM</span>
        <span class="stat-value text-slate-800">{{
          formatNumber(completed)
        }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-emerald-500"
            :style="{ width: pctCompleted + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-emerald-600">{{ pctCompleted }}%</span>
        </div>
        <span class="stat-note"
          >* Selain Open & Draft (Submit + Approve + Reject)</span
        >
      </div> -->

      <!-- STATUS TERBUKA (OPEN) -->
      <div class="stat-card">
        <span class="stat-label">Status OPEN</span>
        <span class="stat-value text-amber-500">{{ formatNumber(open) }}</span>
        <div class="stat-bar-wrapper">
          <div class="stat-bar bg-yellow-500" :style="{ width: pctOpen + '%' }"></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-amber-500">{{ pctOpen }}%</span>
        </div>
      </div>

      <!-- SUBMITTED BY PENCACAH -->
      <div class="stat-card">
        <span class="stat-label">Submitted by Pencacah</span>
        <span class="stat-value text-green-600">{{ formatNumber(submitted_p) }}</span>
        <div class="stat-bar-wrapper">
          <div
            class="stat-bar bg-green-500"
            :style="{ width: pctSubmittedP + '%' }"
          ></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-green-600">{{ pctSubmittedP }}%</span>
        </div>
      </div>

      <!-- APPROVED BY PENGAWAS -->
      <div class="stat-card">
        <span class="stat-label">Approved by Pengawas</span>
        <span class="stat-value text-green-600">{{ formatNumber(approved) }}</span>
        <div class="stat-bar-wrapper">
          <div class="stat-bar bg-green-500" :style="{ width: pctApproved + '%' }"></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-green-600">{{ pctApproved }}%</span>
        </div>
      </div>

      <!-- STATUS DRAFT -->
      <div class="stat-card">
        <span class="stat-label">Status Draft</span>
        <span class="stat-value text-blue-600">{{ formatNumber(draft) }}</span>
        <div class="stat-bar-wrapper">
          <div class="stat-bar bg-blue-500" :style="{ width: pctDraft + '%' }"></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-blue-600">{{ pctDraft }}%</span>
        </div>
      </div>

      <!-- REJECTED BY PENGAWAS -->
      <div class="stat-card">
        <span class="stat-label">Rejected by Pengawas</span>
        <span class="stat-value text-red-600">{{ formatNumber(rejected) }}</span>
        <div class="stat-bar-wrapper">
          <div class="stat-bar bg-red-500" :style="{ width: pctRejected + '%' }"></div>
        </div>
        <div class="stat-footer">
          <span class="stat-pct text-red-600">{{ pctRejected }}%</span>
        </div>
      </div>
    </div>
    <div class="rank-container mb-4">
      <div class="rank-header justify-between">
        <div class="flex items-center gap-2">
          <i class="pi pi-history text-lg text-orange-500"></i>
          <div>
            <h2 class="text-base font-bold text-slate-800">Update Terakhir</h2>
            <p class="text-xs text-slate-500">User yang melakukan 3 update terakhir:</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Badge
            v-for="(item, index) in lastThreeUpdate"
            :key="index"
            :value="
              item.pegawai.username +
              ', (Kode ' +
              item.kode +
              ', ' +
              item.formatted_time +
              ')'
            "
            :severity="getSeverity(item.level)"
          />
        </div>
      </div>
    </div>
    <div class="rank-container mb-4">
      <div class="tab-nav">
        <button
          class="tab-btn"
          :class="{ 'tab-active': activeTab === 'wilayah' }"
          @click="activeTab = 'wilayah'"
        >
          <i class="pi pi-map"></i>
          Wilayah
        </button>
        <button
          class="tab-btn"
          :class="{ 'tab-active': activeTab === 'ppl' }"
          @click="switchTab('ppl')"
        >
          <i class="pi pi-users"></i>
          PPL
        </button>
        <button
          class="tab-btn"
          :class="{ 'tab-active': activeTab === 'pml' }"
          @click="switchTab('pml')"
        >
          <i class="pi pi-user"></i>
          PML
        </button>
      </div>
      <div
        class="flex flex-wrap items-end mt-4 space-x-2"
        v-if="activeTab === 'ppl' || activeTab === 'pml'"
      >
        <div class="flex flex-col gap-2">
          <label class="font-bold">Kabupaten/Kota</label>
          <Select
            :options="kabkot"
            v-model="selectedKabkot"
            @change="fetchKec"
            filter
            showClear
            placeholder="Pilih kabupaten/kota"
            optionLabel="label"
            optionValue="code"
            class="w-64"
          />
        </div>
        <div class="flex flex-col gap-2">
          <label class="font-bold">Kecamatan</label>
          <Select
            :options="kec"
            v-model="selectedKec"
            @change="fetchDesa"
            filter
            showClear
            placeholder="Pilih kecamatan"
            optionLabel="label"
            optionValue="code"
            class="w-64"
          />
        </div>
        <div class="flex flex-col gap-2">
          <label class="font-bold">Desa</label>
          <Select
            :options="desa"
            v-model="selectedDesa"
            @change="fetchSls"
            filter
            showClear
            placeholder="Pilih desa"
            optionLabel="label"
            optionValue="code"
            class="w-64"
          />
        </div>
        <div class="flex flex-col gap-2">
          <label class="font-bold">SLS</label>
          <Select
            :options="sls"
            v-model="selectedSls"
            filter
            showClear
            placeholder="Pilih sls"
            optionLabel="label"
            optionValue="code"
            class="w-64"
          />
        </div>
        <div class="space-x-2">
          <Button @click="fetchDataPetugas(activeTab)" icon="pi pi-search" class="mb-0" />
        </div>
      </div>
    </div>
    <!-- TAB: WILAYAH -->
    <div v-if="activeTab === 'wilayah'" class="rank-container">
      <div class="rank-header justify-between">
        <div class="flex items-center gap-2">
          <i class="pi pi-chart-line text-lg text-orange-500"></i>
          <div>
            <h2 class="text-base font-bold text-slate-800">Progres Realisasi</h2>
            <p class="text-xs text-slate-500">
              Distribusi persentase realisasi (Selain status Open dan Draft)
            </p>
          </div>
        </div>
        <div class="space-x-2">
          <Select
            :pt="{
              label: { class: 'text-sm px-2 py-1' },
              option: { class: 'text-sm px-2 py-1' },
            }"
            v-model="sortMode"
            :options="sortOptions"
            optionLabel="label"
            optionValue="value"
            placeholder="Urutkan"
            size="small"
            class="text-xs"
          />
          <Badge
            @click="goToRegion('71')"
            class="cursor-pointer"
            :severity="getSeverity('kabkot')"
            value="Level Kab/Kota"
          />
          <Badge
            @click="goToRegion(currentCode, 'kec')"
            v-if="getVisibility('kec')"
            class="cursor-pointer"
            :severity="getSeverity('kec')"
            value="Level Kecamatan"
          />
          <Badge
            @click="goToRegion(currentCode, 'desa')"
            v-if="getVisibility('desa')"
            class="cursor-pointer"
            :severity="getSeverity('desa')"
            value="Level Desa"
          />
          <Badge
            v-if="getVisibility('sls')"
            class="cursor-pointer"
            :severity="getSeverity('sls')"
            value="Level SLS"
          />
        </div>
      </div>
      <div class="flex gap-x-6 mt-4 overflow-x-auto pb-2">
        <div
          v-for="(col, colIdx) in columns"
          :key="colIdx"
          class="flex-shrink-0"
          style="min-width: 320px; flex: 1"
        >
          <div
            v-for="(item, idx) in col"
            :key="item.code"
            class="rank-item cursor-pointer"
            @click="goToRegion(item.code)"
          >
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <span class="rank-number" :class="rankColor(colIdx * 15 + idx)">{{
                colIdx * 15 + idx + 1
              }}</span>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <span class="rank-name">{{ item.name }}</span>
                  <div class="flex items-center gap-2">
                    <span class="rank-stats"
                      >{{ formatNumber(kabkotRealisasi(item)) }} /
                      {{ formatNumber(kabkotTotal(item)) }}</span
                    >
                    <span class="rank-pct" :class="rankPctColor(colIdx * 15 + idx)"
                      >{{ kabkotPct(item) }}%</span
                    >
                  </div>
                </div>
                <div class="rank-bar-wrapper">
                  <div
                    class="rank-bar"
                    :class="rankBarColor(colIdx * 15 + idx)"
                    :style="{ width: kabkotPct(item) + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB: PPL -->
    <div v-if="activeTab === 'ppl'" class="rank-container">
      <div class="rank-header justify-between mb-4">
        <div class="flex items-center gap-2">
          <i class="pi pi-users text-lg text-orange-500"></i>
          <div>
            <h2 class="text-base font-bold text-slate-800">Progres Per PPL</h2>
            <p class="text-xs text-slate-500">Klik baris untuk melihat detail per SLS</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Button
            v-if="page.props.auth.role == 'operator'"
            title="Upload Nama Petugas"
            @click="openUploadDialog"
            rounded
            icon="pi pi-upload"
            size="small"
            severity="success"
          />
          <Button
            v-if="page.props.auth.role == 'operator'"
            title="Update PPL"
            @click="updatePetugas(activeTab)"
            rounded
            icon="pi pi-refresh"
            size="small"
            severity="warn"
          />
          <Button
            title="Download"
            @click="downloadPetugas"
            rounded
            icon="pi pi-download"
            size="small"
            severity="info"
          />
          <InputText
            v-model="petugasSearchNama"
            placeholder="Cari nama/email PPL (Enter untuk cari)"
            size="small"
            class="text-sm w-96"
            @keyup.enter="fetchDataPetugas(activeTab)"
          />
          <Button
            icon="pi pi-search"
            size="small"
            severity="secondary"
            @click="fetchDataPetugas(activeTab)"
          />
        </div>
      </div>

      <!-- Loading state -->
      <!-- <div
        v-if="petugasLoading"
        class="flex items-center justify-center py-12 text-slate-400"
      >
        <i class="pi pi-spin pi-spinner text-2xl mr-3"></i>
        <span class="text-sm">Memuat data PPL...</span>
      </div> -->

      <!-- DataTable -->
      <!-- v-else-if="paginatedItem?.data" -->
      <DataTable
        v-if="paginatedItem?.data"
        :value="paginatedItem.data"
        class="w-full text-sm"
        lazy
        paginator
        showGridlines
        stripedRows
        v-model:expandedRows="expandedRows"
        dataKey="id"
        :rows="paginatedItem.per_page"
        :first="(paginatedItem.current_page - 1) * paginatedItem.per_page"
        :total-records="paginatedItem.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortFieldPetugas"
        :sort-order="sortOrderPetugas"
        @page="onPetugasPage"
        @sort="onPetugasSort"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <template #empty>
          <div class="text-center py-6 text-slate-400">
            <i class="pi pi-inbox text-3xl mb-2"></i>
            <p>Data Tidak Ada</p>
          </div>
        </template>
        <Column expander style="width: 3rem" />
        <Column header="No" style="width: 3.5rem">
          <template #body="{ index }">
            <span class="text-xs text-slate-500">
              {{ (paginatedItem.current_page - 1) * paginatedItem.per_page + index + 1 }}
            </span>
          </template>
        </Column>
        <Column field="nama" header="Nama">
          <template #body="{ data }">
            <div>
              <span class="font-semibold text-slate-800">{{ data.nama || "-" }}</span>
              <p class="text-xs text-slate-400">{{ data.email }}</p>
            </div>
          </template>
        </Column>
        <Column header="Total SLS" style="width: 5rem; text-align: center">
          <template #body="{ data }">
            <Badge :value="data.fasih?.length || 0" severity="info" />
          </template>
        </Column>
        <Column header="Realisasi" sortable field="realisasi" style="width: 8rem">
          <template #body="{ data }">
            <div class="flex flex-col gap-1">
              <div class="flex justify-between text-xs">
                <span class="text-emerald-600 font-bold">{{
                  petugasRealisasi(data)
                }}</span>
                <span class="text-slate-400">/ {{ petugasTotal(data) }}</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div
                  class="h-full bg-emerald-500 rounded-full transition-all"
                  :style="{ width: petugasPct(data) + '%' }"
                ></div>
              </div>
              <span class="text-[10px] text-emerald-600 font-semibold text-right"
                >{{ petugasPct(data) }}%</span
              >
            </div>
          </template>
        </Column>

        <!-- EXPANDED ROW: per-SLS detail -->
        <template #expansion="{ data }">
          <div class="p-3 bg-slate-50/80">
            <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
              <i class="pi pi-list mr-1"></i>
              Detail SLS — {{ data.nama || data.email }}
            </h4>
            <DataTable
              :value="data.fasih"
              class="text-xs"
              showGridlines
              stripedRows
              size="small"
            >
              <Column field="subsls_code" header="Kode SLS" style="width: 10rem">
                <template #body="{ data: row }">
                  <span class="font-mono text-xs">{{ row.subsls_code }}</span>
                </template>
              </Column>
              <Column field="open" header="Open" style="width: 4rem; text-align: center">
                <template #body="{ data: row }">
                  <Badge
                    :value="row.open || 0"
                    :severity="row.open > 0 ? 'warn' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="draft"
                header="Draft"
                style="width: 4rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.draft || 0"
                    :severity="row.draft > 0 ? 'info' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="submitted_p"
                header="Submit (by Pencacah)"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.submitted_p || 0"
                    :severity="row.submitted_p > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="submitted_r"
                header="Submit (by Respondent)"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.submitted_r || 0"
                    :severity="row.submitted_r > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="approved"
                header="Approved"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.approved || 0"
                    :severity="row.approved > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="rejected"
                header="Rejected"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.rejected || 0"
                    :severity="row.rejected > 0 ? 'danger' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="revoked"
                header="Revoked"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.revoked || 0"
                    :severity="row.revoked > 0 ? 'warn' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="completed"
                header="Completed"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.completed || 0"
                    :severity="row.completed > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>

      <!-- Empty initial state -->
      <!-- <div v-else class="flex flex-col items-center justify-center py-12 text-slate-400">
        <i class="pi pi-users text-4xl mb-3"></i>
        <p class="text-sm">Data PPL akan dimuat otomatis</p>
      </div> -->
    </div>

    <!-- TAB: PML (placeholder) -->
    <div v-if="activeTab === 'pml'" class="rank-container">
      <div class="rank-header justify-between mb-4">
        <div class="flex items-center gap-2">
          <i class="pi pi-users text-lg text-orange-500"></i>
          <div>
            <h2 class="text-base font-bold text-slate-800">Progres Per PML</h2>
            <p class="text-xs text-slate-500">Klik baris untuk melihat detail per SLS</p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Button
            v-if="page.props.auth.role == 'operator'"
            title="Upload Nama Petugas"
            @click="openUploadDialog"
            rounded
            icon="pi pi-upload"
            size="small"
            success="secondary"
          />
          <Button
            v-if="page.props.auth.role == 'operator'"
            title="Update PML"
            @click="updatePetugas(activeTab)"
            rounded
            icon="pi pi-refresh"
            size="small"
            severity="warn"
          />
          <Button
            title="Download"
            @click="downloadPetugas"
            rounded
            icon="pi pi-download"
            size="small"
            severity="info"
          />
          <InputText
            v-model="petugasSearchNama"
            placeholder="Cari nama/email PPL (Enter untuk cari)"
            size="small"
            class="text-sm w-96"
            @keyup.enter="fetchDataPetugas(activeTab)"
          />
          <Button
            icon="pi pi-search"
            size="small"
            severity="secondary"
            @click="fetchDataPetugas(activeTab)"
          />
        </div>
      </div>
      <DataTable
        v-if="paginatedItem?.data"
        :value="paginatedItem.data"
        class="w-full text-sm"
        lazy
        paginator
        showGridlines
        stripedRows
        v-model:expandedRows="expandedRows"
        dataKey="id"
        :rows="paginatedItem.per_page"
        :first="(paginatedItem.current_page - 1) * paginatedItem.per_page"
        :total-records="paginatedItem.total"
        :rows-per-page-options="[10, 20, 50, 100]"
        :removable-sort="true"
        :sort-field="sortFieldPetugas"
        :sort-order="sortOrderPetugas"
        @page="onPetugasPage"
        @sort="onPetugasSort"
        paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        current-page-report-template="Menampilkan {first} s.d {last} dari {totalRecords} data"
      >
        <template #empty>
          <div class="text-center py-6 text-slate-400">
            <i class="pi pi-inbox text-3xl mb-2"></i>
            <p>Data Tidak Ada</p>
          </div>
        </template>
        <Column expander style="width: 3rem" />
        <Column header="No" style="width: 3.5rem">
          <template #body="{ index }">
            <span class="text-xs text-slate-500">
              {{ (paginatedItem.current_page - 1) * paginatedItem.per_page + index + 1 }}
            </span>
          </template>
        </Column>
        <Column field="nama" header="Nama">
          <template #body="{ data }">
            <div>
              <span class="font-semibold text-slate-800">{{ data.nama || "-" }}</span>
              <p class="text-xs text-slate-400">{{ data.email }}</p>
            </div>
          </template>
        </Column>
        <Column header="Total SLS" style="width: 5rem; text-align: center">
          <template #body="{ data }">
            <Badge :value="data.fasih?.length || 0" severity="info" />
          </template>
        </Column>
        <Column header="Realisasi" sortable field="realisasi" style="width: 8rem">
          <template #body="{ data }">
            <div class="flex flex-col gap-1">
              <div class="flex justify-between text-xs">
                <span class="text-emerald-600 font-bold">{{
                  petugasRealisasi(data)
                }}</span>
                <span class="text-slate-400">/ {{ petugasTotal(data) }}</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div
                  class="h-full bg-emerald-500 rounded-full transition-all"
                  :style="{ width: petugasPct(data) + '%' }"
                ></div>
              </div>
              <span class="text-[10px] text-emerald-600 font-semibold text-right"
                >{{ petugasPct(data) }}%</span
              >
            </div>
          </template>
        </Column>

        <!-- EXPANDED ROW: per-SLS detail -->
        <template #expansion="{ data }">
          <div class="p-3 bg-slate-50/80">
            <h4 class="text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
              <i class="pi pi-list mr-1"></i>
              Detail SLS — {{ data.nama || data.email }}
            </h4>
            <DataTable
              :value="data.fasih"
              class="text-xs"
              showGridlines
              stripedRows
              size="small"
            >
              <Column field="subsls_code" header="Kode SLS" style="width: 10rem">
                <template #body="{ data: row }">
                  <span class="font-mono text-xs">{{ row.subsls_code }}</span>
                </template>
              </Column>
              <Column field="open" header="Open" style="width: 4rem; text-align: center">
                <template #body="{ data: row }">
                  <Badge
                    :value="row.open || 0"
                    :severity="row.open > 0 ? 'warn' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="draft"
                header="Draft"
                style="width: 4rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.draft || 0"
                    :severity="row.draft > 0 ? 'info' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="submitted_p"
                header="Submit (by Pencacah)"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.submitted_p || 0"
                    :severity="row.submitted_p > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="submitted_r"
                header="Submit (by Respondent)"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.submitted_r || 0"
                    :severity="row.submitted_r > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="approved"
                header="Approved"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.approved || 0"
                    :severity="row.approved > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="rejected"
                header="Rejected"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.rejected || 0"
                    :severity="row.rejected > 0 ? 'danger' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="revoked"
                header="Revoked"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.revoked || 0"
                    :severity="row.revoked > 0 ? 'warn' : 'secondary'"
                  />
                </template>
              </Column>
              <Column
                field="completed"
                header="Completed"
                style="width: 5rem; text-align: center"
              >
                <template #body="{ data: row }">
                  <Badge
                    :value="row.completed || 0"
                    :severity="row.completed > 0 ? 'success' : 'secondary'"
                  />
                </template>
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>
    </div>
    <Dialog
      v-model:visible="uploadDialog"
      modal
      :closable="!isUploading"
      :header="dialogTitle"
      class="w-[95vw] max-w-[600px]"
      position="top"
    >
      <div class="flex flex-col gap-4">
        <!-- FILE SELECTION (hidden during upload) -->
        <div v-if="!isUploading && !isCompleted">
          <label class="block font-bold text-sm mb-2">Pilih File Petugas</label>
          <p class="text-xs text-slate-500 mb-3">
            Bisa pilih banyak file sekaligus. Format nama:
            <code class="bg-slate-100 px-1 rounded text-orange-600"
              >{ppl atau pml}_7171.xlsx</code
            >
          </p>
          <input
            ref="fileInput"
            type="file"
            accept=".csv,.xlsx,.xls"
            multiple
            @change="onFilesSelected"
            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 file:cursor-pointer file:transition-colors"
          />
        </div>

        <!-- FILE LIST PREVIEW -->
        <div
          v-if="selectedFiles.length > 0"
          class="border border-slate-200 rounded-xl overflow-hidden"
        >
          <div
            class="bg-slate-50 px-4 py-2 flex items-center justify-between border-b border-slate-200"
          >
            <span class="text-xs font-bold text-slate-600 uppercase tracking-wide"
              >{{ selectedFiles.length }} file dipilih</span
            >
            <span
              v-if="isUploading || isCompleted"
              class="text-xs font-semibold"
              :class="{
                'text-blue-600': isUploading,
                'text-emerald-600': isCompleted,
              }"
            >
              {{ completedCount }}/{{ selectedFiles.length }} selesai
            </span>
          </div>

          <!-- OVERALL PROGRESS BAR -->
          <div v-if="isUploading || isCompleted" class="px-4 pt-3 pb-1">
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500 ease-out"
                :class="hasErrors ? 'bg-amber-500' : 'bg-emerald-500'"
                :style="{ width: overallProgress + '%' }"
              ></div>
            </div>
            <p class="text-xs text-slate-500 mt-1 text-right">{{ overallProgress }}%</p>
          </div>

          <div class="max-h-[40vh] overflow-y-auto">
            <div
              v-for="(file, idx) in selectedFiles"
              :key="idx"
              class="flex items-center gap-3 px-4 py-2.5 border-b border-slate-100 last:border-b-0 transition-colors"
              :class="{
                'bg-blue-50/50': file.status === 'uploading',
                'bg-emerald-50/40': file.status === 'success',
                'bg-red-50/40': file.status === 'error',
              }"
            >
              <!-- Status icon -->
              <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                <!-- Pending -->
                <i
                  v-if="file.status === 'pending'"
                  class="pi pi-clock text-slate-400 text-sm"
                ></i>
                <!-- Uploading (spinning) -->
                <i
                  v-else-if="file.status === 'uploading'"
                  class="pi pi-spin pi-spinner text-blue-600 text-sm"
                ></i>
                <!-- Success -->
                <i
                  v-else-if="file.status === 'success'"
                  class="pi pi-check-circle text-emerald-600 text-sm"
                ></i>
                <!-- Error -->
                <i
                  v-else-if="file.status === 'error'"
                  class="pi pi-times-circle text-red-500 text-sm"
                ></i>
                <!-- Cancelled -->
                <i
                  v-else-if="file.status === 'cancelled'"
                  class="pi pi-ban text-slate-400 text-sm"
                ></i>
              </div>

              <!-- File info -->
              <div class="flex-1 min-w-0">
                <p
                  class="text-sm font-medium truncate"
                  :class="{
                    'text-slate-800': file.status === 'pending',
                    'text-blue-700': file.status === 'uploading',
                    'text-emerald-700': file.status === 'success',
                    'text-red-600': file.status === 'error',
                    'text-slate-400': file.status === 'cancelled',
                  }"
                >
                  {{ file.name }}
                </p>
                <p v-if="file.status === 'success'" class="text-xs text-emerald-600">
                  {{ file.rowsProcessed }} baris diproses dari
                  {{ file.rowsTotal }}
                </p>
                <p v-if="file.status === 'error'" class="text-xs text-red-500">
                  {{ file.errorMessage }}
                </p>
                <p v-if="file.status === 'uploading'" class="text-xs text-blue-500">
                  Sedang memproses...
                </p>
                <p v-if="file.status === 'cancelled'" class="text-xs text-slate-400">
                  Dibatalkan
                </p>
              </div>

              <!-- Remove button (only before upload starts) -->
              <button
                v-if="!isUploading && !isCompleted"
                @click="removeFile(idx)"
                class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
              >
                <i class="pi pi-times text-xs"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- SUMMARY (after completion) -->
        <div
          v-if="isCompleted"
          class="rounded-xl p-4 border"
          :class="
            hasErrors
              ? 'bg-amber-50 border-amber-200'
              : 'bg-emerald-50 border-emerald-200'
          "
        >
          <div class="flex items-center gap-2 mb-2">
            <i
              class="pi text-lg"
              :class="
                hasErrors
                  ? 'pi-exclamation-triangle text-amber-600'
                  : 'pi-check-circle text-emerald-600'
              "
            ></i>
            <span
              class="font-bold text-sm"
              :class="hasErrors ? 'text-amber-800' : 'text-emerald-800'"
            >
              Upload Selesai
            </span>
          </div>
          <div class="grid grid-cols-3 gap-2 text-center">
            <div class="bg-white/70 rounded-lg p-2">
              <p class="text-lg font-bold text-emerald-600">{{ successCount }}</p>
              <p class="text-xs text-slate-500">Berhasil</p>
            </div>
            <div class="bg-white/70 rounded-lg p-2">
              <p class="text-lg font-bold text-red-500">{{ errorCount }}</p>
              <p class="text-xs text-slate-500">Gagal</p>
            </div>
            <div class="bg-white/70 rounded-lg p-2">
              <p class="text-lg font-bold text-slate-400">{{ cancelledCount }}</p>
              <p class="text-xs text-slate-500">Dibatalkan</p>
            </div>
          </div>
        </div>
      </div>

      <template #footer>
        <!-- Before upload -->
        <div v-if="!isUploading && !isCompleted" class="flex justify-end gap-2">
          <Button
            label="Batal"
            @click="closeDialog"
            size="small"
            severity="secondary"
            outlined
          />
          <Button
            @click="startBatchUpload"
            label="Upload Semua"
            size="small"
            severity="success"
            icon="pi pi-upload"
            :disabled="selectedFiles.length === 0"
          />
        </div>

        <!-- During upload -->
        <div v-if="isUploading" class="flex justify-end gap-2">
          <Button
            label="Batalkan Sisa"
            @click="cancelUpload"
            size="small"
            severity="danger"
            icon="pi pi-stop"
            outlined
          />
        </div>

        <!-- After completion -->
        <div v-if="isCompleted" class="flex justify-end gap-2">
          <Button
            label="Tutup"
            @click="closeAndRefresh"
            size="small"
            severity="info"
            icon="pi pi-check"
          />
        </div>
      </template>
    </Dialog>
  </SeLayout>
</template>

<script setup>
import SpinnerBorder from "@/Components/ManManagement/SpinnerBorder.vue";
import SeLayout from "@/Layouts/Se2026/SeLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";
import * as XLSX from "xlsx";

const page = usePage();
const uploadDialog = ref(false);
const props = defineProps({
  open: Number,
  draft: Number,
  submitted_p: Number,
  submitted_r: Number,
  approved: Number,
  rejected: Number,
  revoked: Number,
  completed: Number,
  total: Number,
  data_progress: Array,
  current_level: String,
  lastThreeUpdate: Object,
  kabkot: Array,
});

const selectedKabkot = ref(null);
const selectedKec = ref(null);
const selectedDesa = ref(null);
const selectedSls = ref(null);
const kec = ref([]);
const desa = ref([]);
const sls = ref([]);
const fetchKec = async () => {
  const { data } = await axios.get(
    route("se2026.fetch-kec", { kabkot: selectedKabkot.value })
  );
  kec.value = data;
};

const fetchDesa = async () => {
  const { data } = await axios.get(
    route("se2026.fetch-desa", { kec: selectedKec.value })
  );
  desa.value = data;
};

const fetchSls = async () => {
  const { data } = await axios.get(
    route("se2026.fetch-sls", { desa: selectedDesa.value })
  );
  sls.value = data;
};

const activeTab = ref("wilayah"); // wilayah | ppl | pml

const switchTab = async (tab) => {
  activeTab.value = tab;
  if (tab === "ppl" || tab === "pml") {
    await fetchDataPetugas(tab);
  }
  // PML: implement later
};

const progressData = ref(props.data_progress);
const currentLevel = ref(props.current_level);

const sortMode = ref("code");
const sortOptions = [
  { label: "Kode Wilayah", value: "code" },
  { label: "Realisasi Terbesar", value: "realisasi" },
  { label: "Target Terbesar", value: "target" },
];

const getTotal = (item) => {
  return (
    (item.open || 0) +
    (item.draft || 0) +
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

const getRealisasi = (item) => {
  return (
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

// Chunk data into groups of 15, sorted by selected mode
const columns = computed(() => {
  const data = [...(progressData.value || [])];

  if (sortMode.value === "code") {
    data.sort((a, b) => String(a.code).localeCompare(String(b.code)));
  } else if (sortMode.value === "realisasi") {
    data.sort((a, b) => {
      const pctA = getTotal(a) ? getRealisasi(a) / getTotal(a) : 0;
      const pctB = getTotal(b) ? getRealisasi(b) / getTotal(b) : 0;
      return pctB - pctA;
    });
  } else if (sortMode.value === "target") {
    data.sort((a, b) => getTotal(b) - getTotal(a));
  }

  const chunks = [];
  for (let i = 0; i < data.length; i += 15) {
    chunks.push(data.slice(i, i + 15));
  }
  return chunks;
});

const kabkotTotal = (item) => {
  return (
    (item.open || 0) +
    (item.draft || 0) +
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

const kabkotRealisasi = (item) => {
  return (
    (item.submitted_p || 0) +
    (item.submitted_r || 0) +
    (item.approved || 0) +
    (item.rejected || 0) +
    (item.revoked || 0) +
    (item.completed || 0)
  );
};

const kabkotPct = (item) => {
  const tot = kabkotTotal(item);
  if (tot === 0) return "0.00";
  return ((kabkotRealisasi(item) / tot) * 100).toFixed(2);
};

const rankColor = (idx) => {
  return "rank-default";
};

const rankPctColor = (idx) => {
  return "text-emerald-600";
};

const rankBarColor = (idx) => {
  return "bg-emerald-400";
};

const getSeverity = (level) => {
  if (level == currentLevel.value) return "info";
  else return "secondary";
};
const getVisibility = (level) => {
  if (
    level == "kabkot" &&
    (currentLevel.value == "kabkot" ||
      currentLevel.value == "kec" ||
      currentLevel.value == "desa" ||
      currentLevel.value == "sls")
  )
    return true;
  if (
    level == "kec" &&
    (currentLevel.value == "kec" ||
      currentLevel.value == "desa" ||
      currentLevel.value == "sls")
  )
    return true;
  if (level == "desa" && (currentLevel.value == "desa" || currentLevel.value == "sls"))
    return true;
  if (level == "sls" && currentLevel.value == "sls") return true;
  return false;
};

// Realisasi = semua selain Open
const realisasi = computed(
  () =>
    (props.submitted_p || 0) +
    (props.submitted_r || 0) +
    (props.approved || 0) +
    (props.rejected || 0) +
    (props.revoked || 0) +
    (props.completed || 0)
);

const pct = (val) => {
  if (!props.total || props.total === 0) return "0.00";
  return ((val / props.total) * 100).toFixed(2);
};

const pctRealisasi = computed(() => pct(realisasi.value));
const pctCompleted = computed(() => pct(props.completed || 0));
const pctOpen = computed(() => pct(props.open || 0));
const pctSubmittedP = computed(() => pct(props.submitted_p || 0));
const pctApproved = computed(() => pct(props.approved || 0));
const pctDraft = computed(() => pct(props.draft || 0));
const pctRejected = computed(() => pct(props.rejected || 0));

const formatNumber = (n) => {
  if (n == null) return "0";
  return n.toLocaleString("id-ID");
};

const currentCode = ref("71");
const goToRegion = async (code, type = null) => {
  currentCode.value = code;
  if (type == "kec") currentCode.value = code.substring(0, 4);
  if (type == "desa") currentCode.value = code.substring(0, 7);
  if (code.length > 10) return;
  try {
    const { data } = await axios.get(route("se2026.data.index"), {
      params: {
        code: currentCode.value,
      },
    });
    progressData.value = data.data_progress;
    currentLevel.value = data.current_level;
  } catch (error) {
    console.error(error);
  }
};

const paginatedItem = ref(null);
const petugasCurrentPage = ref(1);
const petugasPageSize = ref(10);
const sortFieldPetugas = ref(null);
const sortOrderPetugas = ref(null);
const expandedRows = ref({});
const petugasLoading = ref(false);
const petugasSearchNama = ref("");

const thisTriggerSpinner = ref(false);
const fetchDataPetugas = async (tab) => {
  petugasLoading.value = true;
  thisTriggerSpinner.value = true;
  try {
    const { data } = await axios.get(route(`se2026.fetch-data-${tab}`), {
      params: {
        currentPage: petugasCurrentPage.value,
        paginated: petugasPageSize.value,
        sortField: sortFieldPetugas.value,
        sortOrder: sortOrderPetugas.value,
        nama: petugasSearchNama.value || undefined,
        kabkot: selectedKabkot.value || undefined,
        kec: selectedKec.value || undefined,
        desa: selectedDesa.value || undefined,
        sls: selectedSls.value || undefined,
      },
    });
    paginatedItem.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    petugasLoading.value = false;
    thisTriggerSpinner.value = false;
  }
};

const downloadPetugas = () => {
  const url =
    route(`se2026.download-report-petugas`) +
    "?" +
    new URLSearchParams({
      kabkot: selectedKabkot.value || "",
      kec: selectedKec.value || "",
      desa: selectedDesa.value || "",
      sls: selectedSls.value || "",
      nama: petugasSearchNama.value || "",
      tab: activeTab.value,
    });
  window.location.href = url;
};

const onPetugasPage = (event) => {
  petugasCurrentPage.value = Math.floor(event.first / event.rows) + 1;
  petugasPageSize.value = event.rows;
  fetchDataPetugas(activeTab.value);
};

const onPetugasSort = (event) => {
  sortFieldPetugas.value = event.sortField;
  sortOrderPetugas.value = event.sortOrder;
  fetchDataPetugas(activeTab.value);
};

const petugasTotal = (petugas) => {
  if (!petugas.fasih) return 0;
  return petugas.fasih.reduce((sum, f) => {
    return (
      sum +
      Number(f.open || 0) +
      Number(f.draft || 0) +
      Number(f.submitted_p || 0) +
      Number(f.submitted_r || 0) +
      Number(f.approved || 0) +
      Number(f.rejected || 0) +
      Number(f.revoked || 0) +
      Number(f.completed || 0)
    );
  }, 0);
};

const petugasRealisasi = (petugas) => {
  if (!petugas.fasih) return 0;
  return petugas.fasih.reduce((sum, f) => {
    return (
      sum +
      Number(f.submitted_p || 0) +
      Number(f.submitted_r || 0) +
      Number(f.approved || 0) +
      Number(f.rejected || 0) +
      Number(f.revoked || 0) +
      Number(f.completed || 0)
    );
  }, 0);
};

const petugasPct = (petugas) => {
  const tot = petugasTotal(petugas);
  if (tot === 0) return "0.00";
  return ((petugasRealisasi(petugas) / tot) * 100).toFixed(2);
};

const updatePetugas = async (tab) => {
  thisTriggerSpinner.value = true;
  try {
    await axios.get(route("se2026.update-petugas", { petugas: tab }));
    await fetchDataPetugas(tab);
  } catch (error) {
    console.error(error);
  } finally {
    thisTriggerSpinner.value = false;
  }
};

const selectedFiles = ref([]);
const isUploading = ref(false);
const isCompleted = ref(false);
const cancelRequested = ref(false);
const dialogTitle = computed(() => {
  if (isCompleted.value) return "Hasil Upload";
  if (isUploading.value)
    return `Mengupload... (${completedCount.value}/${selectedFiles.value.length})`;
  return "Upload Data Terbaru";
});
const completedCount = computed(
  () =>
    selectedFiles.value.filter((f) => f.status === "success" || f.status === "error")
      .length
);
const successCount = computed(
  () => selectedFiles.value.filter((f) => f.status === "success").length
);
const errorCount = computed(
  () => selectedFiles.value.filter((f) => f.status === "error").length
);
const cancelledCount = computed(
  () => selectedFiles.value.filter((f) => f.status === "cancelled").length
);
const hasErrors = computed(() => errorCount.value > 0 || cancelledCount.value > 0);
const overallProgress = computed(() => {
  if (selectedFiles.value.length === 0) return 0;
  const done = selectedFiles.value.filter(
    (f) => f.status !== "pending" && f.status !== "uploading"
  ).length;
  return Math.round((done / selectedFiles.value.length) * 100);
});
const openUploadDialog = () => {
  selectedFiles.value = [];
  isUploading.value = false;
  isCompleted.value = false;
  cancelRequested.value = false;
  uploadDialog.value = true;
};
const closeDialog = () => {
  uploadDialog.value = false;
  selectedFiles.value = [];
};

const closeAndRefresh = async () => {
  uploadDialog.value = false;
  selectedFiles.value = [];
  // window.location.reload();
  await fetchDataPetugas(activeTab.value);
};

const onFilesSelected = (e) => {
  const files = Array.from(e.target.files || []);
  selectedFiles.value = files.map((file) => ({
    raw: file,
    name: file.name,
    status: "pending",
    parsedData: null,
    rowsProcessed: 0,
    rowsTotal: 0,
    errorMessage: "",
  }));
};
const removeFile = (idx) => {
  selectedFiles.value.splice(idx, 1);
};
const parseFile = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const dataBuffer = e.target.result;
        const wb = XLSX.read(dataBuffer, { type: "array" });
        const wsname = wb.SheetNames[0];
        const ws = wb.Sheets[wsname];
        const data = XLSX.utils.sheet_to_json(ws, { header: 1 });
        resolve(data);
      } catch (err) {
        reject(err);
      }
    };
    reader.onerror = () => reject(new Error("Gagal membaca file"));
    reader.readAsArrayBuffer(file);
  });
};
const startBatchUpload = async () => {
  if (selectedFiles.value.length === 0) return;

  isUploading.value = true;
  isCompleted.value = false;
  cancelRequested.value = false;

  // Get CSRF token once
  let csrfToken;
  try {
    const { data: token } = await axios.get(route("api.token.csrf"));
    csrfToken = token;
  } catch (err) {
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "Gagal mendapatkan CSRF token",
      life: 3000,
    });
    isUploading.value = false;
    return;
  }

  // Process files one by one
  for (let i = 0; i < selectedFiles.value.length; i++) {
    // Check if cancel was requested
    if (cancelRequested.value) {
      // Mark remaining files as cancelled
      for (let j = i; j < selectedFiles.value.length; j++) {
        selectedFiles.value[j].status = "cancelled";
      }
      break;
    }

    const fileEntry = selectedFiles.value[i];
    fileEntry.status = "uploading";

    try {
      // Parse the CSV file
      const parsedData = await parseFile(fileEntry.raw);
      fileEntry.parsedData = parsedData;

      // Send to backend
      const response = await axios.post(route("se2026.upload-petugas-batch"), {
        _token: csrfToken,
        file: parsedData,
        file_name: fileEntry.name,
      });

      fileEntry.status = "success";
      fileEntry.rowsProcessed = response.data.rows_processed;
      fileEntry.rowsTotal = response.data.rows_total;
    } catch (err) {
      fileEntry.status = "error";
      fileEntry.errorMessage =
        err.response?.data?.message || err.message || "Terjadi kesalahan tidak diketahui";
    }

    // Free memory
    fileEntry.parsedData = null;
  }

  isUploading.value = false;
  isCompleted.value = true;

  // Show summary toast
  if (successCount.value > 0) {
    toast.add({
      severity: hasErrors.value ? "warn" : "success",
      summary: "Upload Selesai",
      detail: `${successCount.value} berhasil, ${errorCount.value} gagal, ${cancelledCount.value} dibatalkan`,
      life: 5000,
    });
  }
};

const cancelUpload = () => {
  cancelRequested.value = true;
};
</script>

<style scoped>
.stat-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(148, 163, 184, 0.15), transparent);
}

.stat-card:hover {
  border-color: #cbd5e1;
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.stat-label {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
}

.stat-value {
  font-size: 2.25rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.02em;
}

.stat-bar-wrapper {
  width: 100%;
  height: 6px;
  background: #e2e8f0;
  border-radius: 999px;
  overflow: hidden;
}

.stat-bar {
  height: 100%;
  border-radius: 999px;
  transition: width 1s cubic-bezier(0.22, 1, 0.36, 1);
  min-width: 4px;
}

.stat-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.stat-pct {
  font-size: 0.85rem;
  font-weight: 700;
}

.stat-note {
  font-size: 0.6rem;
  color: #64748b;
  line-height: 1.3;
}

/* Rank Section */
.rank-container {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.rank-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.rank-badge {
  font-size: 0.65rem;
  font-weight: 600;
  color: #64748b;
  background: #f1f5f9;
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  border: 1px solid #e2e8f0;
}

.rank-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0.5rem;
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.2s ease;
}

.rank-item:hover {
  background: #f8fafc;
  border-radius: 8px;
}

.rank-number {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  flex-shrink: 0;
}

.rank-top {
  background: #f97316;
  color: #ffffff;
}

.rank-default {
  background: #f1f5f9;
  color: #64748b;
}

.rank-name {
  font-size: 0.8rem;
  font-weight: 700;
  color: #1e293b;
  text-transform: uppercase;
}

.rank-stats {
  font-size: 0.7rem;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
}

.rank-pct {
  font-size: 0.75rem;
  font-weight: 700;
  white-space: nowrap;
}

.rank-bar-wrapper {
  width: 100%;
  height: 4px;
  background: #e2e8f0;
  border-radius: 999px;
  overflow: hidden;
}

.rank-bar {
  height: 100%;
  border-radius: 999px;
  transition: width 1s cubic-bezier(0.22, 1, 0.36, 1);
  min-width: 2px;
}

/* Tab Navigation */
.tab-nav {
  display: flex;
  gap: 0.25rem;
  background: #f1f5f9;
  border-radius: 12px;
  padding: 0.25rem;
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.25rem;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.tab-btn:hover {
  color: #334155;
  background: rgba(255, 255, 255, 0.5);
}

.tab-active {
  color: #ea580c !important;
  background: #ffffff !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
</style>
