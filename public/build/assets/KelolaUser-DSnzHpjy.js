import{r,q as E,j as e,Y as M,y as m}from"./app-BKJY4YWz.js";import{A as k}from"./AuthenticatedLayout-CJgoqSmC.js";import{B as S,S as v,P as A}from"./Pagination-DQDND7Ex.js";import O from"./TableUser-BKYQ9nKF.js";import C from"./AddUserForm-DfzzmhSr.js";import N from"./EditUserForm-pCAb3zzq.js";import{A as p}from"./Alert-Bo3OTTma.js";import"./index-Itt_kyJq.js";import"./index-BWEEZemA.js";import"./index-CF2UqtEp.js";import"./motion-Db2xci-V.js";import"./zoom-j3DgVdf6.js";import"./index-CHILXjkW.js";import"./useLocale-L6Wt6RyJ.js";import"./index-DpbQZBri.js";import"./index-fnTQ2R4P.js";import"./PurePanel-BwRILxwh.js";import"./row-ZHVTgW77.js";import"./index-CYwRI9iO.js";import"./index-DWJMk0Vo.js";import"./index-C5fZKDoR.js";import"./BaseInput-CvXDMhyo.js";import"./SearchOutlined-M6lAbnbo.js";import"./index-DoEnEz9a.js";const te=({auth:o,users:t,search:c})=>{const[d,a]=r.useState(!1),[x,i]=r.useState(!1),[u,f]=r.useState(null),{flash:l,errors:n}=E().props,j=()=>a(!0),h=()=>a(!1),g=()=>i(!1),b=s=>{f(s),i(!0)},U=s=>{m.delete(`/users/${s}`).then(()=>{})},y=s=>{m.get("/users",{search:s},{replace:!0})};return e.jsxs(k,{title:"User",user:o.user,children:[l.success&&e.jsx(p,{tipe:"success",pesan:l.success}),n&&Object.keys(n).length>0&&e.jsx(p,{tipe:"error",pesan:"Terjadi kesalahan! Silahkan cek form."}),e.jsx(M,{title:"Kelola Users"}),e.jsx(S,{pageName:"Kelola User"}),e.jsxs("div",{className:"flex flex-row sm:justify-between mb-5",children:[e.jsx(v,{initialQuery:c,handleSearch:y}),e.jsx("div",{children:e.jsx("button",{onClick:j,className:" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:"Tambah User"})})]}),e.jsxs("div",{className:"flex flex-col gap-10",children:[e.jsx(O,{users:t,onEdit:b,onDelete:U,role:o.user.role}),e.jsx(A,{links:t.links,currentPage:t.current_page,lastPage:t.last_page,from:t.from,to:t.to,total:t.total})]}),e.jsx(C,{visible:d,onCancel:h}),e.jsx(N,{visible:x,onCancel:g,user:u})]})};export{te as default};