import{r,q as y,j as e,Y as E,y as m}from"./app-D_OB5lB0.js";import{A as M}from"./AuthenticatedLayout-BViSrbYa.js";import{B as S}from"./Breadcrumb-CQBnXcKy.js";import{S as v,P as A}from"./Pagination-CPSxFH7K.js";import O from"./TableUser-ZTSgqoK5.js";import C from"./AddUserForm-BuH1MsQv.js";import N from"./EditUserForm-Cnh-Ot2Z.js";import{A as p}from"./Alert-BKNCSh6i.js";import"./Header-56Cko1Qe.js";import"./index-CJaVn-ZZ.js";import"./index-CbOp89og.js";import"./zoom-jj1jlWM7.js";import"./button-DwurTV-5.js";import"./index-DwLMJULd.js";import"./ActionButton-GlizxOHx.js";import"./useLocale-DgW6ErAE.js";import"./index-Bw6sYPsB.js";import"./index-DKvSL8C2.js";import"./PurePanel-BBLJmiT4.js";import"./index-CdhNzL3b.js";import"./row-AI_3Apdy.js";import"./index-BL4lJwOz.js";import"./index-pdXJTcLb.js";import"./DownOutlined-eKTYEdw_.js";const te=({auth:o,users:t,search:c})=>{const[d,a]=r.useState(!1),[x,i]=r.useState(!1),[u,f]=r.useState(null),{flash:l,errors:n}=y().props,j=()=>a(!0),h=()=>a(!1),g=()=>i(!1),b=s=>{f(s),i(!0)},U=s=>{m.delete(route("singkat.admin.users.destroy",{user:s})).then(()=>{})},k=s=>{m.get(route("singkat.admin.users"),{search:s},{replace:!0})};return e.jsxs(M,{title:"User",user:o.user,children:[l.success&&e.jsx(p,{tipe:"success",pesan:l.success}),n&&Object.keys(n).length>0&&e.jsx(p,{tipe:"error",pesan:"Terjadi kesalahan! Silahkan cek form."}),e.jsx(E,{title:"Kelola Users"}),e.jsx(S,{pageName:"Kelola User"}),e.jsxs("div",{className:"flex flex-row sm:justify-between mb-5",children:[e.jsx(v,{initialQuery:c,handleSearch:k}),e.jsx("div",{children:e.jsx("button",{onClick:j,className:" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:"Tambah User"})})]}),e.jsxs("div",{className:"flex flex-col gap-10",children:[e.jsx(O,{users:t,onEdit:b,onDelete:U,role:o.user.role}),e.jsx(A,{links:t.links,currentPage:t.current_page,lastPage:t.last_page,from:t.from,to:t.to,total:t.total})]}),e.jsx(C,{visible:d,onCancel:h}),e.jsx(N,{visible:x,onCancel:g,user:u})]})};export{te as default};