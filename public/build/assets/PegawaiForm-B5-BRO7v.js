import{r as b,j as e,a as C}from"./app-qozg50Md.js";import{d as V}from"./dayjs.min-kBRJ-rpy.js";import{M as F}from"./index-BSk5wdO_.js";import{F as l}from"./index-aOYu-vQh.js";import{I as P}from"./index-DC3t1Uft.js";import{S as f}from"./index-DTsYXN2-.js";import{T as $}from"./index-DZ7bMjgQ.js";import{S as H}from"./index-BP0t2nwt.js";import{f as W}from"./warning-Bf_VSDRp.js";import{g as G,m as Y,r as q,u as p,C as U}from"./index-Cl5Xqjrl.js";import{D as R}from"./index-xlaZMbuE.js";import"./button-Bf_he-TK.js";import"./AntdIcon-oE3BgIqN.js";import"./PurePanel-CDwIu1rJ.js";import"./zoom-DM4q-JEj.js";import"./ActionButton-CVruMRtK.js";import"./useLocale-BH1iDomU.js";import"./row-D3mj2rtc.js";import"./index-CJyQPicH.js";import"./EyeOutlined-BduE5REH.js";import"./index-B-kb_xka.js";import"./DownOutlined-DKhpsX0H.js";const X=t=>{const{componentCls:a,sizePaddingEdgeHorizontal:m,colorSplit:r,lineWidth:o,textPaddingInline:k,orientationMargin:u,verticalMarginInline:d}=t;return{[a]:Object.assign(Object.assign({},q(t)),{borderBlockStart:`${p(o)} solid ${r}`,"&-vertical":{position:"relative",top:"-0.06em",display:"inline-block",height:"0.9em",marginInline:d,marginBlock:0,verticalAlign:"middle",borderTop:0,borderInlineStart:`${p(o)} solid ${r}`},"&-horizontal":{display:"flex",clear:"both",width:"100%",minWidth:"100%",margin:`${p(t.dividerHorizontalGutterMargin)} 0`},[`&-horizontal${a}-with-text`]:{display:"flex",alignItems:"center",margin:`${p(t.dividerHorizontalWithTextGutterMargin)} 0`,color:t.colorTextHeading,fontWeight:500,fontSize:t.fontSizeLG,whiteSpace:"nowrap",textAlign:"center",borderBlockStart:`0 ${r}`,"&::before, &::after":{position:"relative",width:"50%",borderBlockStart:`${p(o)} solid transparent`,borderBlockStartColor:"inherit",borderBlockEnd:0,transform:"translateY(50%)",content:"''"}},[`&-horizontal${a}-with-text-left`]:{"&::before":{width:`calc(${u} * 100%)`},"&::after":{width:`calc(100% - ${u} * 100%)`}},[`&-horizontal${a}-with-text-right`]:{"&::before":{width:`calc(100% - ${u} * 100%)`},"&::after":{width:`calc(${u} * 100%)`}},[`${a}-inner-text`]:{display:"inline-block",paddingBlock:0,paddingInline:k},"&-dashed":{background:"none",borderColor:r,borderStyle:"dashed",borderWidth:`${p(o)} 0 0`},[`&-horizontal${a}-with-text${a}-dashed`]:{"&::before, &::after":{borderStyle:"dashed none none"}},[`&-vertical${a}-dashed`]:{borderInlineStartWidth:o,borderInlineEnd:0,borderBlockStart:0,borderBlockEnd:0},[`&-plain${a}-with-text`]:{color:t.colorText,fontWeight:"normal",fontSize:t.fontSize},[`&-horizontal${a}-with-text-left${a}-no-default-orientation-margin-left`]:{"&::before":{width:0},"&::after":{width:"100%"},[`${a}-inner-text`]:{paddingInlineStart:m}},[`&-horizontal${a}-with-text-right${a}-no-default-orientation-margin-right`]:{"&::before":{width:"100%"},"&::after":{width:0},[`${a}-inner-text`]:{paddingInlineEnd:m}}})}},Q=t=>({textPaddingInline:"1em",orientationMargin:.05,verticalMarginInline:t.marginXS}),Z=G("Divider",t=>{const a=Y(t,{dividerHorizontalWithTextGutterMargin:t.margin,dividerHorizontalGutterMargin:t.marginLG,sizePaddingEdgeHorizontal:0});return[X(a)]},Q,{unitless:{orientationMargin:!0}});var ee=function(t,a){var m={};for(var r in t)Object.prototype.hasOwnProperty.call(t,r)&&a.indexOf(r)<0&&(m[r]=t[r]);if(t!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,r=Object.getOwnPropertySymbols(t);o<r.length;o++)a.indexOf(r[o])<0&&Object.prototype.propertyIsEnumerable.call(t,r[o])&&(m[r[o]]=t[r[o]]);return m};const ae=t=>{const{getPrefixCls:a,direction:m,divider:r}=b.useContext(U),{prefixCls:o,type:k="horizontal",orientation:u="center",orientationMargin:d,className:T,rootClassName:n,children:c,dashed:S,plain:y,style:I}=t,j=ee(t,["prefixCls","type","orientation","orientationMargin","className","rootClassName","children","dashed","plain","style"]),s=a("divider",o),[J,i,g]=Z(s),h=!!c,v=u==="left"&&d!=null,x=u==="right"&&d!=null,w=W(s,r==null?void 0:r.className,i,g,`${s}-${k}`,{[`${s}-with-text`]:h,[`${s}-with-text-${u}`]:h,[`${s}-dashed`]:!!S,[`${s}-plain`]:!!y,[`${s}-rtl`]:m==="rtl",[`${s}-no-default-orientation-margin-left`]:v,[`${s}-no-default-orientation-margin-right`]:x},T,n),M=b.useMemo(()=>typeof d=="number"?d:/^\d+$/.test(d)?Number(d):d,[d]),_=Object.assign(Object.assign({},v&&{marginLeft:M}),x&&{marginRight:M});return J(b.createElement("div",Object.assign({className:w,style:Object.assign(Object.assign({},r==null?void 0:r.style),I)},j,{role:"separator"}),c&&k!=="vertical"&&b.createElement("span",{className:`${s}-inner-text`,style:_},c)))},{RangePicker:te}=R,re=(t,a)=>a?/^\d{18}$/.test(a)?Promise.resolve():Promise.reject(new Error("NIP harus terdiri dari 18 angka.")):Promise.reject(new Error("NIP tidak boleh kosong.")),ne=(t,a)=>a?/^\d{9}$/.test(a)?Promise.resolve():Promise.reject(new Error("NIP BPS harus tediri dari 9 digit angka.")):Promise.reject(new Error("NIP BPS tidak boleh kosong.")),$e=({visible:t,onCancel:a,pegawai:m,jabatan:r,unitKerja:o,role:k,onFinish:u,type:d,title:T,form:n})=>{const c=async()=>{await n.validateFields();const i=Number(n.getFieldValue("angka_kredit_dasar"))||0,g=n.getFieldValue("jabatan_id"),h=n.getFieldValue("ijazah_terakhir"),v=n.getFieldValue("is_ijazah_calculated");if(!g||!h||!n.getFieldValue("bulan")){n.setFieldValue("akumulasi_ak",i);return}const{fromDate:x,toDate:w}={fromDate:new Date(n.getFieldValue("bulan")[0]),toDate:new Date(n.getFieldValue("bulan")[1])},{data:M}=await C.get(`/api/jabatans/${g}`),_=M.angka_kredit||0,N={"SD/sederajat":0,"SLTP/sederajat":0,"SLTA/sederajat":0,DI:0,DII:0,DIII:0,"S1/DIV/sederajat":0,S2:1,S3:1};let z=0;h&&N.hasOwnProperty(h)&&v&&(z=_*N[h]);const O=x.getMonth(),E=w.getMonth(),B=w.getFullYear()-x.getFullYear(),K=E-O+1+B*12,A=n.getFieldValue("predikat_id");let D=0;try{D=_*S[A-1].nilai*K/12}catch{}const L=i+z+D;n.setFieldValue("akumulasi_ak",Number(L).toFixed(4))},[S,y]=b.useState([]),[I,j]=b.useState(!1),s=async()=>{try{const{data:i}=await C.get("/api/predikats");y(i)}catch{console.error("Error when get predikat data")}};b.useEffect(()=>{s();async function i(){try{const{data:g}=await C.get(route("api.token.csrf"));n.setFieldValue("_token",g)}catch{console.log("error get token")}}i()},[]);const J=[{label:"Juru Muda/Ia",value:"Juru Muda/Ia"},{label:"Juru Muda Tingkat I/Ib",value:"Juru Muda Tingkat I/Ib"},{label:"Juru/Ic",value:"Juru/Ic"},{label:"Juru Tingkat I/Id",value:"Juru Tingkat I/Id"},{label:"Juru Pengatur Muda/IIa",value:"Juru Pengatur Muda/IIa"},{label:"Juru Pengatur Muda Tingkat I/IIb",value:"Juru Pengatur Muda Tingkat I/IIb"},{label:"Juru Pengatur/IIc",value:"Juru Pengatur/IIc"},{label:"Juru Pengatur Tingkat I/IId",value:"Juru Pengatur Tingkat I/IId"},{label:"Juru Penata Muda/IIIa",value:"Juru Penata Muda/IIIa"},{label:"Juru Penata Muda Tingkat I/IIIb",value:"Juru Penata Muda Tingkat I/IIIb"},{label:"Juru Penata/IIIc",value:"Juru Penata/IIIc"},{label:"Juru Penata Tingkat I/IIId",value:"Juru Penata Tingkat I/IIId"},{label:"Juru Pembina/IVa",value:"Juru Pembina/IVa"},{label:"Juru Pembina Tingkat I/IVb",value:"Juru Pembina Tingkat I/IVb"},{label:"Juru Pembina Tingkat Utama Muda/IVc",value:"Juru Pembina Tingkat Utama Muda/IVc"},{label:"Juru Pembina Tingkat Utama Madya/IVd",value:"Juru Pembina Tingkat Utama Madya/IVd"},{label:"Juru Pembina Tingkat Utama/IVe",value:"Juru Pembina Tingkat Utama/IVe"}];return e.jsx(F,{title:`${T} ${n.getFieldValue("nama")}  (${n.getFieldValue("nip_bps")})`,open:t,style:{top:20},onCancel:()=>{j(!1),a()},onOk:()=>{n.submit(),j(!1)},width:600,okText:"Simpan",cancelText:"Batal",children:e.jsxs(l,{form:n,onFinish:u,layout:"vertical",wrapperCol:{span:24},autoComplete:"off",onKeyDown:i=>{i.code==="Enter"&&n.submit()},size:"large",children:[e.jsx(l.Item,{name:"id",hidden:!0,children:e.jsx(P,{})}),e.jsx(l.Item,{name:"_token",hidden:!0,children:e.jsx(P,{})}),e.jsx(l.Item,{name:"nip_bps",label:"NIP BPS",rules:[{required:!0,validator:ne}],hidden:d==="edit",children:e.jsx(P,{placeholder:"Masukkan NIP lama contoh : 32002098",className:"border border-slate-400 rounded-md"})}),e.jsx(l.Item,{name:"nip",label:"NIP",rules:[{required:!0,validator:re}],hidden:d==="edit",children:e.jsx(P,{placeholder:"Masukkan NIP baru contoh : 198810232001041002",className:"border border-slate-400 rounded-md"})}),e.jsx(l.Item,{name:"nama",label:"Nama Pegawai",rules:[{required:!0}],hidden:d==="edit",children:e.jsx(P,{placeholder:"Nama Lengkap Tanpa Singkatan",className:"border border-slate-400 rounded-md"})}),e.jsx(l.Item,{name:"unit_kerja",label:"Satuan Kerja",rules:[{required:!0}],className:"focus:border-none",children:e.jsx(f,{placeholder:"Pilih Satuan Kerja",allowClear:!0,showSearch:!0,optionFilterProp:"label",options:o.map(i=>({label:i.nama,value:i.nama}))})}),e.jsx(l.Item,{name:"jabatan_id",label:"Jabatan",rules:[{required:!0}],className:"focus:border-none",children:e.jsx(f,{allowClear:!0,showSearch:!0,placeholder:"Pilih Jabatan Pegawai",optionFilterProp:"label",onChange:()=>{F.confirm({title:"Konfirmasi",content:"Mengubah jabatan pegawai akan mereset akumulasi angka kredit menjadi 0 (nol) !",footer:(i,{OkBtn:g,CancelBtn:h})=>e.jsx(e.Fragment,{children:e.jsx(g,{})}),onOk:()=>{n.setFieldValue("angka_kredit_dasar",0),n.setFieldValue("akumulasi_ak",0),j(!0)}}),c()},options:r.map(i=>({label:i.nama,value:String(i.id)}))})}),e.jsx(l.Item,{name:"pangkat_golongan_ruang",label:"Pangkat / Golongan Ruang",rules:[{required:!0}],children:e.jsx(f,{allowClear:!0,showSearch:!0,placeholder:"Penata Muda/IIIa",className:"border border-slate-400 rounded-md",options:J})}),e.jsx(l.Item,{name:"angka_kredit_konvensional",label:"Angka Kredit Konvensional",hidden:d==="edit",children:e.jsx($,{className:"border w-[30%] border-slate-400 rounded-md"})}),e.jsx(l.Item,{name:"angka_kredit_integrasi",label:"Angka Kredit Integrasi",hidden:d==="edit",children:e.jsx($,{className:"border w-[30%] border-slate-400 rounded-md"})}),e.jsx(l.Item,{name:"angka_kredit_dasar",label:"Angka Kredit Dasar / Terakhir",className:"focus:border-none",onChange:()=>c(),children:e.jsx($,{className:"border border-slate-400 rounded-md"})}),e.jsx(l.Item,{name:"ijazah_terakhir",label:"Ijazah Terakhir",hidden:I,children:e.jsx(f,{onSelect:()=>{n.setFieldValue("is_ijazah_calculated",!0),c()},placeholder:"Pilih Ijazah yang Ditamatkan",options:[{label:"SD/sederajat",value:"SD/sederajat"},{label:"SLTP/sederajat",value:"SLTP/sederajat"},{label:"SLTA/sederajat",value:"SLTA/sederajat"},{label:"DI",value:"DI"},{label:"DII",value:"DII"},{label:"DIII",value:"DIII"},{label:"S1/DIV",value:"S1/DIV/sederajat"},{label:"S2",value:"S2"},{label:"S3",value:"S3"}]})}),e.jsx(l.Item,{name:"is_ijazah_calculated",label:"Tambahkan sebagai angka kredit",hidden:I,children:e.jsx(H,{onChange:c})}),e.jsx(l.Item,{name:"bulan",label:"Bulan Penilaian",hidden:I,children:e.jsx(te,{picker:"month",minDate:V("2019-01-01"),format:"MMMM YYYY",maxDate:V(),onChange:c})}),e.jsx(l.Item,{name:"predikat_id",label:"Predikat Kinerja",className:"focus:border-none",hidden:I,children:e.jsx(f,{placeholder:"Pilih Predikat",allowClear:!0,showSearch:!0,optionFilterProp:"label",onChange:c,options:S.map(i=>({label:i.nama,value:String(i.id)}))})}),e.jsx(ae,{}),e.jsx(l.Item,{name:"akumulasi_ak",label:"Akumulasi Angka Kredit",children:e.jsx($,{readOnly:!0,className:"border border-slate-400 rounded-md"})})]})})};export{$e as default};
