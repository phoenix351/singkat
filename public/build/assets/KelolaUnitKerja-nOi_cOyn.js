import{r,q as y,j as e,Y as E,y as n}from"./app-BKJY4YWz.js";import{A as K}from"./AuthenticatedLayout-CJgoqSmC.js";import{B as M,S as U,P as v}from"./Pagination-DQDND7Ex.js";import A from"./TableUnitKerja-BHe7dmUy.js";import O from"./AddUnitKerjaForm-BFxSlCGs.js";import C from"./EditUnitKerjaForm-DfDSoDva.js";import{A as m}from"./Alert-Bo3OTTma.js";import"./index-Itt_kyJq.js";import"./index-BWEEZemA.js";import"./index-CF2UqtEp.js";import"./motion-Db2xci-V.js";import"./zoom-j3DgVdf6.js";import"./index-CHILXjkW.js";import"./useLocale-L6Wt6RyJ.js";import"./index-DpbQZBri.js";import"./index-fnTQ2R4P.js";import"./PurePanel-BwRILxwh.js";import"./row-ZHVTgW77.js";import"./index-CYwRI9iO.js";import"./index-DWJMk0Vo.js";import"./index-C5fZKDoR.js";import"./BaseInput-CvXDMhyo.js";import"./SearchOutlined-M6lAbnbo.js";const ee=({auth:p,unitKerja:t,search:c})=>{const[d,a]=r.useState(!1),[x,o]=r.useState(!1),[u,j]=r.useState(null),{flash:i,errors:l}=y().props,f=()=>a(!0),h=()=>a(!1),g=()=>o(!1),b=s=>{j(s),o(!0)},S=s=>{console.log(s),n.delete(`/unit-kerja/${s}`).then(()=>{})},k=s=>{n.get("/unit-kerja",{search:s},{replace:!0})};return e.jsxs(K,{title:"Unit Kerja",user:p.user,children:[i.success&&e.jsx(m,{tipe:"success",pesan:i.success}),l&&Object.keys(l).length>0&&e.jsx(m,{tipe:"error",pesan:"Terjadi kesalahan! Silahkan cek form."}),e.jsx(E,{title:"Kelola Satuan Kerja"}),e.jsx(M,{pageName:"Kelola Satuan Kerja"}),e.jsxs("div",{className:"flex flex-row sm:justify-between mb-5",children:[e.jsx(U,{initialQuery:c,handleSearch:k}),e.jsx("div",{children:e.jsx("button",{onClick:f,className:" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:"Tambah Satuan Kerja"})})]}),e.jsxs("div",{className:"flex flex-col gap-10",children:[e.jsx(A,{unitKerja:t,onEdit:b,onDelete:S}),e.jsx(v,{links:t.links,currentPage:t.current_page,lastPage:t.last_page,from:t.from,to:t.to,total:t.total})]}),e.jsx(O,{visible:d,onCancel:h}),e.jsx(C,{visible:x,onCancel:g,unitKerja:u})]})};export{ee as default};