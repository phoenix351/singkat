import{q as p,r as d,j as e,y as c,a as x}from"./app-BCXNxsvu.js";import{F as r}from"./index-CeOsWrVz.js";import{M as b}from"./index-DqBy_bOg.js";import{I as a}from"./index-CCBrNHrs.js";import{S as h}from"./index-DT3GEK5Q.js";import"./PurePanel-BvOf297_.js";import"./index-jkWDoZuv.js";import"./button-DYp94kPC.js";import"./zoom-Dv1Wj_dI.js";import"./row-DByOmR57.js";import"./index-Ctvgr8PY.js";import"./useLocale-BdT_xuzA.js";import"./ActionButton-BA_aWDSj.js";import"./index-CqUmIfNG.js";import"./DownOutlined-DM4bYnaF.js";const V=({visible:n,onCancel:m,user:o})=>{const[s]=r.useForm(),{errors:t}=p().props;d.useEffect(()=>{o&&s.setFieldsValue(o)},[o,s]);const u=l=>{c.put(route("singkat.admin.users.update",{user:o.id}),l),m(),s.resetFields()};return d.useEffect(()=>{async function l(){try{const{data:i}=await x.get(route("api.token.csrf"));s.setFieldValue("_token",i)}catch{console.log("error get token")}}l()},[]),e.jsx(b,{title:"Edit User",open:n,onCancel:m,onOk:()=>s.submit(),width:600,children:e.jsxs(r,{form:s,onFinish:u,layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[e.jsx(r.Item,{name:"_token",hidden:!0,children:e.jsx(a,{})}),e.jsx(r.Item,{name:"name",label:"Nama Lengkap",rules:[{required:!0,message:"Nama tidak boleh kosong."}],children:e.jsx(a,{className:"border border-slate-400 rounded-md"})}),e.jsxs(r.Item,{name:"email",label:"Email",rules:[{required:!0,message:"Email tidak boleh kosong."}],children:[e.jsx(a,{className:"border border-slate-400 rounded-md"}),t.email&&e.jsx("div",{className:"text-red-500",children:t.email})]}),e.jsxs(r.Item,{name:"username",label:"Username",rules:[{required:!0,message:"Username tidak boleh kosong."}],children:[e.jsx(a,{className:"border border-slate-400 rounded-md"}),t.username&&e.jsx("div",{className:"text-red-500",children:t.username})]}),e.jsx(r.Item,{name:"role",label:"Role",className:"focus:border-none",children:e.jsx(h,{placeholder:"Pilih Role",options:[{value:"operator",label:"Operator"},{value:"viewer",label:"Viewer"}]})}),e.jsx(r.Item,{name:"password",label:"Password",children:e.jsx(a,{className:"border border-slate-400 rounded-md"})})]})})};export{V as default};
