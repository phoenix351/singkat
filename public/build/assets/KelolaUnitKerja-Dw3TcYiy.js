import{r as s,q as y,j as e,Y as K,y as l}from"./app-BCXNxsvu.js";import{A as E}from"./AuthenticatedLayout-DaLId21e.js";import{B as M}from"./Breadcrumb-B3vwvP4h.js";import{S as U,P as v}from"./Pagination-BOyw80Bh.js";import A from"./TableUnitKerja-DS2hRe2g.js";import O from"./AddUnitKerjaForm-DKzWB6LQ.js";import C from"./EditUnitKerjaForm-3X82pMFi.js";import{A as m}from"./Alert-Dfw58u0m.js";import"./Header-Bjuoj23p.js";import"./index-Ctvgr8PY.js";import"./index-jkWDoZuv.js";import"./zoom-Dv1Wj_dI.js";import"./button-DYp94kPC.js";import"./index-BC_RW6ls.js";import"./ActionButton-BA_aWDSj.js";import"./useLocale-BdT_xuzA.js";import"./index-CqUmIfNG.js";import"./index-CeOsWrVz.js";import"./PurePanel-BvOf297_.js";import"./index-CCBrNHrs.js";import"./row-DByOmR57.js";import"./index-DqBy_bOg.js";const $=({auth:p,unitKerja:t,search:c})=>{const[d,a]=s.useState(!1),[u,o]=s.useState(!1),[x,j]=s.useState(null),{flash:i,errors:n}=y().props,f=()=>a(!0),h=()=>a(!1),g=()=>o(!1),b=r=>{j(r),o(!0)},k=r=>{l.delete(route("singkat.admin.unit-kerja.destroy",{unitKerja:r})).then(()=>{})},S=r=>{l.get(route("singkat.admin.unit-kerja"),{search:r},{replace:!0})};return e.jsxs(E,{title:"Unit Kerja",user:p.user,children:[i.success&&e.jsx(m,{tipe:"success",pesan:i.success}),n&&Object.keys(n).length>0&&e.jsx(m,{tipe:"error",pesan:"Terjadi kesalahan! Silahkan cek form."}),e.jsx(K,{title:"Kelola Satuan Kerja"}),e.jsx(M,{pageName:"Kelola Satuan Kerja"}),e.jsxs("div",{className:"flex flex-row sm:justify-between mb-5",children:[e.jsx(U,{initialQuery:c,handleSearch:S}),e.jsx("div",{children:e.jsx("button",{onClick:f,className:" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:"Tambah Satuan Kerja"})})]}),e.jsxs("div",{className:"flex flex-col gap-10",children:[e.jsx(A,{unitKerja:t,onEdit:b,onDelete:k}),e.jsx(v,{links:t.links,currentPage:t.current_page,lastPage:t.last_page,from:t.from,to:t.to,total:t.total})]}),e.jsx(O,{visible:d,onCancel:h}),e.jsx(C,{visible:u,onCancel:g,unitKerja:x})]})};export{$ as default};