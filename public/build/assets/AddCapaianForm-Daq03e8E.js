import{r as l,j as a,a as i,y as P}from"./app-BKJY4YWz.js";import{c as j}from"./calculateAkumulasiAk-uXEeq9rl.js";import{F as t}from"./index-fnTQ2R4P.js";import{s as S}from"./index-CIfUaNQx.js";import{M as C}from"./index-CYwRI9iO.js";import{S as p}from"./index-DoEnEz9a.js";import{D as F}from"./index-CqrgvYV7.js";import"./PurePanel-BwRILxwh.js";import"./index-BWEEZemA.js";import"./motion-Db2xci-V.js";import"./zoom-j3DgVdf6.js";import"./row-ZHVTgW77.js";import"./index-Itt_kyJq.js";import"./index-CF2UqtEp.js";import"./useLocale-L6Wt6RyJ.js";import"./SearchOutlined-M6lAbnbo.js";import"./index-DpbQZBri.js";import"./dayjs.min-cyMN-Kl5.js";import"./index-C5fZKDoR.js";const G=({visible:m,onCancel:h})=>{const[r]=t.useForm(),[u,d]=l.useState([]),[g,b]=l.useState([]),[s,k]=S.useMessage(),w=(e,o)=>{const n=j(o);r.setFieldsValue({akumulasi_ak:n})},f=async e=>{try{s.open({key:"submit-form",type:"loading",content:"menambahkan capaian pegawai"});let n=`${new Date(e.tahun).getFullYear()}`;const c={...e};c.tahun=n;const T=await i.post("/capaian",c,{headers:{"Content-Type":"application/json"}});s.open({key:"submit-form",type:"success",content:"telah ditambahkan 1 capaian pegawai"})}catch(o){console.log({error:o}),s.open({key:"submit-form",type:"error",content:o.response.data.error})}finally{P.get("/kelola-ckp",{},{preserveState:!0})}},x=async()=>{try{const{data:e}=await i.get("/api/predikats");console.log({data:e}),d(e)}catch{console.error("Error when get predikat data")}},y=async()=>{try{const{data:e}=await i.get("/api/pegawais");console.log({data:e}),b(e)}catch{console.error("Error when get predikat data")}};return l.useEffect(()=>{x(),y()},[]),a.jsxs(a.Fragment,{children:[k,a.jsx(C,{title:"Tambah CKP",open:m,onCancel:h,style:{top:20},onOk:()=>r.submit(),width:600,okText:"Tambah",cancelText:"Batal",children:a.jsxs(t,{form:r,onValuesChange:w,name:"control-hooks",onFinish:f,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[a.jsx(t.Item,{name:"pegawai_id",label:"Pegawai",className:"focus:border-none",children:a.jsx(p,{placeholder:"Pilih Pegawai",allowClear:!0,showSearch:!0,optionFilterProp:"label",options:g.map(e=>({label:e.nama,value:e.id}))})}),a.jsx(t.Item,{name:"predikat",label:"Predikat Kinerja",className:"focus:border-none",children:a.jsx(p,{placeholder:"Pilih Predikat",allowClear:!0,showSearch:!0,optionFilterProp:"label",options:u.map(e=>({label:e.nama,value:e.id}))})}),a.jsx(t.Item,{name:"tahun",label:"Tahun Penilaian",children:a.jsx(F,{picker:"year"})}),a.jsx(t.Item,{name:"periode",label:"Periode Penilaian",children:a.jsx(p,{placeholder:"Pilih Periode",allowClear:!0,showSearch:!0,options:[{label:"Tahunan",value:"Tahunan"},{label:"Semester 1",value:"Semester 1"},{label:"Semester 2",value:"Semester 2"}]})})]})})]})};export{G as default};