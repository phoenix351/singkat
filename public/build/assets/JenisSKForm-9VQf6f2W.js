import{j as e}from"./app-DFnwsWrs.js";import{M as u}from"./index-CxLqXYg-.js";import{F as a}from"./index-D6qsxmpK.js";import{I as i}from"./index-Ch6NPk6K.js";import"./index-7Nr_NS3U.js";import"./button-Bz9USzm7.js";import"./PurePanel-CRgtzXFw.js";import"./zoom-B0W9qZb7.js";import"./ActionButton-CtM_w2vP.js";import"./useLocale-Bol56ME6.js";import"./BaseInput-DmJDSday.js";import"./row-PkYKM1PI.js";import"./index-BbNNQxMk.js";const C=({visible:s,onFinish:t,onCancel:d,title:m,form:r,okText:n,cancelText:l})=>e.jsx(u,{title:m,open:s,onCancel:d,onOk:()=>r.submit(),width:600,okText:n,cancelText:l,children:e.jsxs(a,{form:r,onFinish:t,layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",onKeyDown:o=>{o.code==="Enter"&&r.submit()},children:[e.jsx(a.Item,{name:"id",label:"ID",hidden:!0,children:e.jsx(i,{className:"border border-slate-400 rounded-md"})}),e.jsx(a.Item,{name:"new_id",label:"ID Baru",children:e.jsx(i,{className:"border border-slate-400 rounded-md",placeholder:"Isikan ID hanya jika ingin diubah",onChange:o=>{const p=o.target.value.replace(/[^0-9]/g,"");r.setFieldValue("new_id",p)}})}),e.jsx(a.Item,{name:"nama",label:"Deskripsi",rules:[{required:!0,message:"Deskripsi tidak boleh kosong."}],children:e.jsx(i,{className:"border border-slate-400 rounded-md"})})]})});export{C as default};
