import{j as t,y as s}from"./app-DG4lkm6I.js";import{F as a}from"./index-bg-KSOeg.js";import{M as p}from"./index-CP26vVl7.js";import{I as n}from"./index-jsO2WCLV.js";import"./PurePanel-X1WZuywX.js";import"./index-BOoDnPea.js";import"./compact-item-CWVgPv4E.js";import"./zoom-CYFUwcOb.js";import"./row-B-yTj_Is.js";import"./index-DPovuAtR.js";import"./useLocale-Bn5sQBMJ.js";import"./ActionButton-BmFTjVmE.js";import"./button-B7yed1Qm.js";import"./index-CQGu2i8L.js";import"./TextArea-Q31wMMF0.js";import"./BaseInput-QhogEnYX.js";const w=({visible:m,onCancel:r})=>{const[o]=a.useForm(),e=i=>{s.post("/jabatan",i),o.resetFields(),r()};return t.jsx(p,{title:"Tambah Jabatan",open:m,onCancel:r,onOk:()=>o.submit(),width:600,children:t.jsx(a,{form:o,name:"control-hooks",onFinish:e,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:t.jsx(a.Item,{name:"nama",label:"Nama Jabatan",rules:[{required:!0,message:"Nama jabatan tidak boleh kosong."}],children:t.jsx(n,{className:"border border-slate-400 rounded-md"})})})})};export{w as default};