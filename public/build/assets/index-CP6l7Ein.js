import{r as s,j as a,Y as N,a as d,y as i}from"./app-BCXNxsvu.js";import{A as O}from"./AuthenticatedLayout-DaLId21e.js";import D from"./TablePak-BmrxOax2.js";import{B as z}from"./Breadcrumb-B3vwvP4h.js";import{S as V,P as G}from"./Pagination-BOyw80Bh.js";import L from"./ExportModal-Dax7dzpc.js";import g from"./PegawaiForm-C6kclziU.js";import{d as k}from"./dayjs.min-B7SBPmXN.js";import{F as y}from"./index-CeOsWrVz.js";import{s as U}from"./index-CuqvQYHX.js";import"./Header-Bjuoj23p.js";import"./index-Ctvgr8PY.js";import"./index-jkWDoZuv.js";import"./zoom-Dv1Wj_dI.js";import"./button-DYp94kPC.js";import"./index-BC_RW6ls.js";import"./ActionButton-BA_aWDSj.js";import"./useLocale-BdT_xuzA.js";import"./index-CqUmIfNG.js";import"./index-DqBy_bOg.js";import"./PurePanel-BvOf297_.js";import"./index-C938xIb-.js";import"./row-DByOmR57.js";import"./index-CCBrNHrs.js";import"./index-DT3GEK5Q.js";import"./DownOutlined-DM4bYnaF.js";import"./index-Xv2Eix1U.js";import"./index-CzA69XzG.js";const xe=({auth:n,pegawai:r,search:x,jabatan:c,unitKerja:u})=>{const[b]=y.useForm(),[l]=y.useForm(),[t,f]=U.useMessage(),[j,p]=s.useState(!1),[_,m]=s.useState(!1),[w,M]=s.useState(null),[S,h]=s.useState(!1),P=()=>p(!0),v=()=>p(!1),E=()=>h(!0),F=()=>h(!1),A=e=>{M(e),m(!0)},C=()=>m(!1),B=async e=>{try{t.open({type:"loading",content:"Menghapus data pegawai",key:"handle-delete"});const o=d.delete(route("singkat.admin.pegawai.destroy",{pegawai:e}));t.open({type:"success",content:"Berhasil menyimpan perubahan",key:"handle-delete"})}catch{t.open({type:"error",content:"Gagal menghapus, periksa kembali !",key:"handle-delete"})}finally{i.reload({preserveState:!0,preserveScroll:!0,method:"get"})}},I=e=>{i.get(route("singkat.admin.pegawai"),{search:e},{replace:!0})},T=async e=>{try{t.open({type:"loading",content:"Menyimpan perubahan",key:"handle-save"}),e.bulan_mulai=e.bulan[0],e.bulan_selesai=e.bulan[1],delete e.bulan;const o=d.put(route("singkat.admin.pegawai.update",{pegawai:e.id}),e,{headers:{"Content-Type":"application/json"}});t.open({type:"success",content:"Berhasil menyimpan perubahan",key:"handle-save"})}catch{t.open({type:"error",content:"Gagal menyimpan, hubungi pengembang web!",key:"handle-save"})}finally{m(!1),i.reload({preserveState:!0,preserveScroll:!0})}},K=async e=>{try{t.open({type:"loading",content:"Menambahkan pegawai",key:"handle-create"}),e.bulan_mulai=e.bulan[0],e.bulan_selesai=e.bulan[1],delete e.bulan;const o=await d.post(route("singkat.admin.pegawai.store"),e,{headers:{"Content-Type":"application/json"}});t.open({type:"success",content:"Berhasil menambahkan pegawai",key:"handle-create"})}catch{t.open({type:"error",content:"Gagal menambahkan, periksa kembali isian !",key:"handle-create"})}finally{p(!1),i.reload({preserveState:!0,preserveScroll:!0})}};return a.jsxs(O,{user:n.user,children:[f,a.jsx(N,{title:n.user.role=="viewer"?"PAK":"Kelola PAK"}),a.jsx(z,{pageName:n.user.role=="viewer"?"PAK":"Kelola PAK"}),a.jsxs("div",{className:"flex flex-row sm:justify-between mb-5",children:[a.jsx(V,{initialQuery:x,handleSearch:I}),a.jsxs("div",{children:[a.jsxs("button",{onClick:E,type:"button",className:"inline-flex items-center justify-center gap-2.5 rounded-md bg-primary py-2 px-5 text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:[a.jsx("span",{children:a.jsx("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24",strokeWidth:1.5,stroke:"currentColor",className:"size-4",children:a.jsx("path",{strokeLinecap:"round",strokeLinejoin:"round",d:"M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"})})}),"Unduh Data"]}),["operator"].includes(n.user.role)&&a.jsx("button",{onClick:()=>{l.resetFields(),l.setFieldsValue({id:340060268,nip_bps:"340060268",nip:"199810132021041001",nama:"Ponimin, S.Tr.Stat.",jabatan_id:"39",unit_kerja:"BPS P",pangkat_golongan_ruang:"Penata Tk.I / IIId",angka_kredit_konvensional:"313.898",angka_kredit_integrasi:"113.898",predikat_kinerja:"Sangat Baik",tambahan_ijazah:null,akumulasi_ak:"151.398",ijazah_terakhir:"S1/DIV/Sederajat",tanggal_lahir:"1971-02-05",nama_jabatan:"Statistisi Ahli Muda",angka_kredit_akumulasi:151.398,tambahan_ijazah:"Baik",bulan:[k(),k()],predikat_id:"1",angka_kredit_dasar:40,akumulasi_ak:47.125}),P()},className:" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:"Tambah Pegawai"})]})]}),a.jsxs("div",{className:"flex flex-col gap-10",children:[a.jsx(D,{pegawai:r,onEdit:A,onDelete:B,role:n.user.role}),a.jsx(G,{links:r.links,currentPage:r.current_page,lastPage:r.last_page,total:r.total,from:r.from,to:r.to})]}),a.jsx(g,{jabatan:c,unitKerja:u,visible:j,onCancel:v,role:n.user.role,onFinish:K,type:"create",title:"Tambah Pegawai",form:l},"create-form"),a.jsx(g,{jabatan:c,unitKerja:u,visible:_,onCancel:C,pegawai:w,role:n.user.role,onFinish:T,type:"edit",title:"Ubah Data Pegawai",form:b},"edit-form"),a.jsx(L,{visible:S,onCancel:F})]})};export{xe as default};