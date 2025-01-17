import{r as s,j as e,Y as J,a as l}from"./app-D_OB5lB0.js";import{A as K}from"./AuthenticatedLayout-BViSrbYa.js";import{B as w}from"./Breadcrumb-CQBnXcKy.js";import m from"./JenisSKForm-DvrXjkwn.js";import{s as E}from"./index-DEkIiiNJ.js";import{F as T}from"./index-DKvSL8C2.js";import{R as F}from"./PlusOutlined-DZ7tDEHl.js";import{F as B}from"./Table-wGPnR-hW.js";import{I,B as h}from"./button-DwurTV-5.js";import{_ as C}from"./index-CbOp89og.js";import{P as O}from"./index-DwLMJULd.js";import{R}from"./DeleteOutlined-DEk5EJjZ.js";import"./Header-56Cko1Qe.js";import"./index-BL4lJwOz.js";import"./PurePanel-BBLJmiT4.js";import"./zoom-jj1jlWM7.js";import"./ActionButton-GlizxOHx.js";import"./useLocale-DgW6ErAE.js";import"./index-CdhNzL3b.js";import"./index-CJaVn-ZZ.js";import"./row-AI_3Apdy.js";import"./index-pdXJTcLb.js";import"./index-Bw6sYPsB.js";import"./DownOutlined-eKTYEdw_.js";import"./index-0GALbpcv.js";import"./useForceUpdate-BfyJsPHQ.js";var A={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M257.7 752c2 0 4-.2 6-.5L431.9 722c2-.4 3.9-1.3 5.3-2.8l423.9-423.9a9.96 9.96 0 000-14.1L694.9 114.9c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2 1-7.1 2.9L256.8 538.8c-1.5 1.5-2.4 3.3-2.8 5.3l-29.5 168.2a33.5 33.5 0 009.4 29.8c6.6 6.4 14.9 9.9 23.8 9.9zm67.4-174.4L687.8 215l73.3 73.3-362.7 362.6-88.9 15.7 15.6-89zM880 836H144c-17.7 0-32 14.3-32 32v36c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-36c0-17.7-14.3-32-32-32z"}}]},name:"edit",theme:"outlined"},H=function(c,o){return s.createElement(I,C({},c,{ref:o,icon:A}))},N=s.forwardRef(H);const pe=({auth:p})=>{const[c,o]=s.useState(!1),[f,d]=s.useState(!1),[u,x]=s.useState([]),[n,y]=E.useMessage(),[r]=T.useForm(),k=()=>{r.resetFields(),o(!0)},j=()=>o(!1),g=()=>d(!1),v=a=>{r.setFieldsValue(a),d(!0)};async function S(a){console.log("asasdadssad");try{n.open({type:"loading",key:"handle-delete",content:"Menghapus data..."});const t=await l.delete(route("singkat.admin.jenis-sk.destroy",{sk:a}));n.open({type:"success",key:"handle-delete",content:"Berhasil menghapus data"})}catch{n.open({type:"error",key:"handle-delete",content:"Terjadi kesalahan"})}finally{i()}}async function i(){try{const{data:a}=await l.get(route("index")+"/api/jenis-sk");x(a)}catch{console.error("failed to fetch data")}}const b=async a=>{try{n.open({type:"loading",key:"handle-add",content:"Menambahkan data..."});const t=await l.post(route("singkat.admin.jenis-sk.store"),a,{headers:{"Content-Type":"application/json"}});n.open({type:"success",key:"handle-add",content:"Berhasil menambahkan data"})}catch(t){n.open({type:"error",key:"handle-add",content:t.response.data.message})}finally{o(!1),i()}},M=async a=>{try{n.open({type:"loading",key:"handle-save",content:"Menyimpan perubahan..."});const{data:t}=await l.put(route("singkat.admin.jenis-sk.update"),a,{headers:{"Content-Type":"application/json"}});t.hasOwnProperty("error")?n.open({type:"error",key:"handle-save",content:t.error}):n.open({type:"success",key:"handle-save",content:t.message})}catch{n.open({type:"error",key:"handle-save",content:"Terjadi kesalahan"})}finally{d(!1),i()}};return s.useEffect(()=>{i()},[]),e.jsxs(K,{title:"Jenis SK",user:p.user,children:[y,e.jsx(J,{title:"Kelola Jenis SK"}),e.jsx(w,{pageName:"Kelola Jenis SK"}),e.jsx("div",{className:"flex flex-row sm:justify-between mb-5",children:e.jsx("div",{children:e.jsxs("button",{onClick:k,className:" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4",children:[e.jsx(F,{})," Tambah Jenis SK"]})})}),e.jsx("div",{className:"flex flex-col gap-10",children:e.jsx(B,{dataSource:u,columns:[{title:"Nomor",dataIndex:"id"},{title:"Jenis SK",dataIndex:"nama"},{title:"Aksi",render:(a,t)=>e.jsxs(h,{onClick:()=>v(t),children:[e.jsx(N,{})," Ubah"]})},{title:"Hapus",render:(a,t)=>e.jsx(O,{title:"Hapus Jenis SK",description:"Apakah anda yakin akan menghapus Jenis SK ini?",onConfirm:()=>{S(t.id)},onCancel:()=>{},children:e.jsxs(h,{children:[e.jsx(R,{})," Hapus"]})})}]})}),e.jsx(m,{visible:c,onCancel:j,onFinish:b,okText:"Tambahkan",cancelText:"Batal",form:r}),e.jsx(m,{visible:f,onCancel:g,onFinish:M,form:r,okText:"Simpan",cancelText:"Batal"})]})};export{pe as default};