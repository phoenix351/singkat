import{r as t,j as e,a as i}from"./app-BKJY4YWz.js";import{s as j}from"./index-CIfUaNQx.js";import{M as g}from"./index-CYwRI9iO.js";import{F as r}from"./index-fnTQ2R4P.js";import{I as k}from"./index-DWJMk0Vo.js";import{S as o}from"./index-DoEnEz9a.js";import{D as S}from"./index-CqrgvYV7.js";import"./index-BWEEZemA.js";import"./zoom-j3DgVdf6.js";import"./motion-Db2xci-V.js";import"./PurePanel-BwRILxwh.js";import"./useLocale-L6Wt6RyJ.js";import"./row-ZHVTgW77.js";import"./index-Itt_kyJq.js";import"./index-CF2UqtEp.js";import"./index-C5fZKDoR.js";import"./BaseInput-CvXDMhyo.js";import"./SearchOutlined-M6lAbnbo.js";import"./index-DpbQZBri.js";import"./dayjs.min-cyMN-Kl5.js";const J=({visible:l,onCancel:n,onFinish:m,form:s,title:p,okText:c,type:d})=>{const[h,u]=t.useState([]),[x,w]=t.useState([]),[y,P]=j.useMessage(),b=async()=>{try{const{data:a}=await i.get("/api/predikats");u(a)}catch{console.error("Error when get predikat data")}},f=async()=>{try{const{data:a}=await i.get("/api/pegawais");w(a)}catch{console.error("Error when get predikat data")}};return t.useEffect(()=>{b(),f()},[]),e.jsxs(e.Fragment,{children:[P,e.jsx(g,{title:p,open:l,onCancel:n,style:{top:20},onOk:()=>s.submit(),width:600,okText:c,cancelText:"Batal",children:e.jsxs(r,{form:s,name:"control-hooks",onFinish:m,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[e.jsx(r.Item,{name:"id",label:"ID",className:"focus:border-none",hidden:!0,children:e.jsx(k,{})}),e.jsx(r.Item,{name:"pegawai_id",label:"Pegawai",className:"focus:border-none",children:e.jsx(o,{placeholder:"Pilih Pegawai",allowClear:!0,showSearch:!0,disabled:d==="edit",optionFilterProp:"label",options:x.map(a=>({label:a.nama,value:a.id}))})}),e.jsx(r.Item,{name:"predikat_id",label:"Predikat Kinerja",className:"focus:border-none",children:e.jsx(o,{placeholder:"Pilih Predikat",allowClear:!0,showSearch:!0,optionFilterProp:"label",options:h.map(a=>({label:a.nama,value:a.id}))})}),e.jsx(r.Item,{name:"tahun",label:"Tahun Penilaian",children:e.jsx(S,{picker:"year"})}),e.jsx(r.Item,{name:"periode",label:"Periode Penilaian",children:e.jsx(o,{placeholder:"Pilih Periode",allowClear:!0,showSearch:!0,options:[{label:"Tahunan",value:"Tahunan"},{label:"Semester 1",value:"Semester 1"},{label:"Semester 2",value:"Semester 2"}]})})]})})]})};export{J as default};
