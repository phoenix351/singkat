import{r as u,a as E,j as a,y as S}from"./app-DFnwsWrs.js";import{F as o}from"./index-D6qsxmpK.js";import{M as w}from"./index-CxLqXYg-.js";import{S as b}from"./index-CdkzHvRD.js";import{T as h}from"./index-zPBp9yDv.js";import"./PurePanel-CRgtzXFw.js";import"./index-7Nr_NS3U.js";import"./button-Bz9USzm7.js";import"./zoom-B0W9qZb7.js";import"./BaseInput-DmJDSday.js";import"./row-PkYKM1PI.js";import"./index-BbNNQxMk.js";import"./useLocale-Bol56ME6.js";import"./ActionButton-CtM_w2vP.js";import"./index-BZT0KOT1.js";import"./DownOutlined-N7VHuyP1.js";const W=({visible:j,onCancel:p,abk:t,jabatan:f,unitKerja:k,role:l})=>{const[i]=o.useForm(),[x,g]=u.useState([]),[y,F]=u.useState(null);u.useEffect(()=>{t&&(i.setFieldsValue(t),t.unit_kerja_id&&c(t.unit_kerja_id,t.jabatan_id))},[t,i]);const _=(e,r)=>{const n=i.getFieldValue("abk");return r>=0&&r<=n?Promise.resolve():Promise.reject(new Error("Nilai Eksisting tidak boleh lebih dari nilai ABK!"))},c=async(e,r=null)=>{F(e);try{let d=(await E.get(route("index")+"/api/get-available-jabatan",{params:{unit_kerja_id:e}})).data.map(s=>({value:s.id,label:s.nama}));if(r){const s=f.find(m=>m.id===r);s&&!d.some(m=>m.value===r)&&d.push({value:s.id,label:s.nama})}g(d)}catch(n){console.error("Failed to fetch jabatan:",n)}},K=e=>{S.put(route("singkat.admin.abk.update",{abk:t.id}),e),p(),i.resetFields()};return a.jsx(w,{title:"Edit ABK",open:j,onCancel:p,onOk:()=>i.submit(),width:600,okText:"Simpan",cancelText:"Batal",children:a.jsxs(o,{form:i,name:"control-hooks",onFinish:K,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[a.jsx(o.Item,{name:"unit_kerja_id",label:"Unit Kerja",rules:[{required:!0,message:"Unit Kerja tidak boleh kosong."}],children:a.jsx(b,{...l==="admin"?{}:{disabled:!0},placeholder:"Pilih Unit Kerja",showSearch:!0,optionFilterProp:"label",onChange:e=>c(e),options:k.map(e=>({value:e.id,label:e.nama}))})}),a.jsx(o.Item,{name:"jabatan_id",label:"Nama Jabatan",rules:[{required:!0,message:"Jabatan tidak boleh kosong."}],children:a.jsx(b,{...l==="admin"?{}:{disabled:!0},placeholder:"Pilih Jabatan",showSearch:!0,optionFilterProp:"label",options:x})}),a.jsx(o.Item,{name:"abk",label:"ABK",rules:[{required:!0,message:"ABK tidak boleh kosong."}],children:a.jsx(h,{...l==="admin"?{}:{disabled:!0},className:"border w-full border-slate-400 rounded-md"})}),a.jsx(o.Item,{rules:[{validator:_}],name:"eksisting",label:"Eksisting",children:a.jsx(h,{className:"border w-full border-slate-400 rounded-md"})})]})})};export{W as default};
