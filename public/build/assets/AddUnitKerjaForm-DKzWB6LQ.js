import{r as l,j as t,y as d,a as p}from"./app-BCXNxsvu.js";import{F as r}from"./index-CeOsWrVz.js";import{M as u}from"./index-DqBy_bOg.js";import{I as i}from"./index-CCBrNHrs.js";import"./PurePanel-BvOf297_.js";import"./index-jkWDoZuv.js";import"./button-DYp94kPC.js";import"./zoom-Dv1Wj_dI.js";import"./row-DByOmR57.js";import"./index-Ctvgr8PY.js";import"./useLocale-BdT_xuzA.js";import"./ActionButton-BA_aWDSj.js";const S=({visible:n,onCancel:a})=>{const[e]=r.useForm(),m=o=>{d.post(route("singkat.admin.unit-kerja.store"),o),e.resetFields(),a()};return l.useEffect(()=>{async function o(){try{const{data:s}=await p.get(route("api.token.csrf"));e.setFieldValue("_token",s)}catch{console.log("error get token")}}o()},[]),t.jsx(u,{title:"Tambah Satuan Kerja",open:n,onCancel:a,onOk:()=>e.submit(),width:600,children:t.jsxs(r,{form:e,name:"control-hooks",onFinish:m,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[t.jsx(r.Item,{name:"_token",hidden:!0,children:t.jsx(i,{})}),t.jsx(r.Item,{name:"nama",label:"Nama Satuan Kerja",rules:[{required:!0,message:"Nama Satuan Kerja tidak boleh kosong."}],children:t.jsx(i,{className:"border border-slate-400 rounded-md"})})]})})};export{S as default};
