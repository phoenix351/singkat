import{r as p,j as e,y as l}from"./app-BCXNxsvu.js";import{F as o}from"./index-CeOsWrVz.js";import{M as d}from"./index-DqBy_bOg.js";import{I as n}from"./index-CCBrNHrs.js";import"./PurePanel-BvOf297_.js";import"./index-jkWDoZuv.js";import"./button-DYp94kPC.js";import"./zoom-Dv1Wj_dI.js";import"./row-DByOmR57.js";import"./index-Ctvgr8PY.js";import"./useLocale-BdT_xuzA.js";import"./ActionButton-BA_aWDSj.js";const y=({visible:s,onCancel:a,jabatan:r})=>{const[t]=o.useForm();p.useEffect(()=>{r&&t.setFieldsValue(r)},[r,t]);const i=m=>{l.put(`/jabatan/${r.id}`,m),a(),t.resetFields()};return e.jsx(d,{title:"Edit Jabatan",open:s,onCancel:a,onOk:()=>t.submit(),width:600,children:e.jsx(o,{form:t,onFinish:i,layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:e.jsx(o.Item,{name:"nama",label:"Nama Jabatan",rules:[{required:!0,message:"Nama Jabatan tidak boleh kosong."}],children:e.jsx(n,{className:"border border-slate-400 rounded-md"})})})})};export{y as default};
