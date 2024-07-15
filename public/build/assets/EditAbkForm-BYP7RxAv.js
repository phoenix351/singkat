import{r as p,a as E,j as a,y as S}from"./app-BGp6Cjwo.js";import{F as s}from"./index-BtE96evI.js";import{M as w}from"./index-b5Y3fyma.js";import{S as b}from"./index-BrLnm6_L.js";import{T as h}from"./index-C1gvinPb.js";import"./PurePanel-BCMIoBXA.js";import"./index-WmajGb9C.js";import"./motion-B8OS_XYX.js";import"./zoom-X00PKVfC.js";import"./row-USzwBVrA.js";import"./index-CZsqTT0k.js";import"./index-Fg_hPEis.js";import"./useLocale-CAzkx_sE.js";import"./SearchOutlined-DBFYwasW.js";import"./index-B-Iu5HGJ.js";import"./BaseInput-BO8apeIw.js";import"./index-B12D2l46.js";const $=({visible:j,onCancel:u,abk:t,jabatan:f,unitKerja:k,role:n})=>{const[i]=s.useForm(),[x,g]=p.useState([]),[y,F]=p.useState(null);p.useEffect(()=>{t&&(i.setFieldsValue(t),t.unit_kerja_id&&c(t.unit_kerja_id,t.jabatan_id))},[t,i]);const _=(e,r)=>{const l=i.getFieldValue("abk");return r>=0&&r<=l?Promise.resolve():Promise.reject(new Error("Nilai Eksisting tidak boleh lebih dari nilai ABK!"))},c=async(e,r=null)=>{F(e);try{let d=(await E.get("/api/get-available-jabatan",{params:{unit_kerja_id:e}})).data.map(o=>({value:o.id,label:o.nama}));if(r){const o=f.find(m=>m.id===r);o&&!d.some(m=>m.value===r)&&d.push({value:o.id,label:o.nama})}g(d)}catch(l){console.error("Failed to fetch jabatan:",l)}},K=e=>{S.put(`/kelola-abk/${t.id}`,e),u(),i.resetFields()};return a.jsx(w,{title:"Edit ABK",open:j,onCancel:u,onOk:()=>i.submit(),width:600,okText:"Simpan",cancelText:"Batal",children:a.jsxs(s,{form:i,name:"control-hooks",onFinish:K,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[a.jsx(s.Item,{name:"unit_kerja_id",label:"Unit Kerja",rules:[{required:!0,message:"Unit Kerja tidak boleh kosong."}],children:a.jsx(b,{...n==="admin"?{}:{disabled:!0},placeholder:"Pilih Unit Kerja",showSearch:!0,optionFilterProp:"label",onChange:e=>c(e),options:k.map(e=>({value:e.id,label:e.nama}))})}),a.jsx(s.Item,{name:"jabatan_id",label:"Nama Jabatan",rules:[{required:!0,message:"Jabatan tidak boleh kosong."}],children:a.jsx(b,{...n==="admin"?{}:{disabled:!0},placeholder:"Pilih Jabatan",showSearch:!0,optionFilterProp:"label",options:x})}),a.jsx(s.Item,{name:"abk",label:"ABK",rules:[{required:!0,message:"ABK tidak boleh kosong."}],children:a.jsx(h,{...n==="admin"?{}:{disabled:!0},className:"border w-full border-slate-400 rounded-md"})}),a.jsx(s.Item,{rules:[{validator:_}],name:"eksisting",label:"Eksisting",children:a.jsx(h,{className:"border w-full border-slate-400 rounded-md"})})]})})};export{$ as default};