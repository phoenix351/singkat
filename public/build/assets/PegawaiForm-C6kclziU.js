import{r as k,j as a,a as h}from"./app-BCXNxsvu.js";import{d as m}from"./dayjs.min-B7SBPmXN.js";import{M as T}from"./index-DqBy_bOg.js";import{F as r}from"./index-CeOsWrVz.js";import{I as s}from"./index-CCBrNHrs.js";import{S as o}from"./index-DT3GEK5Q.js";import{T as I}from"./index-Xv2Eix1U.js";import{D as z}from"./index-CzA69XzG.js";import"./index-jkWDoZuv.js";import"./button-DYp94kPC.js";import"./PurePanel-BvOf297_.js";import"./zoom-Dv1Wj_dI.js";import"./ActionButton-BA_aWDSj.js";import"./useLocale-BdT_xuzA.js";import"./row-DByOmR57.js";import"./index-Ctvgr8PY.js";import"./index-CqUmIfNG.js";import"./DownOutlined-DM4bYnaF.js";const{RangePicker:q}=z,B=(c,n)=>n?/^\d{18}$/.test(n)?Promise.resolve():Promise.reject(new Error("NIP harus terdiri dari 18 angka.")):Promise.reject(new Error("NIP tidak boleh kosong.")),L=(c,n)=>n?/^\d{9}$/.test(n)?Promise.resolve():Promise.reject(new Error("NIP BPS harus tediri dari 9 digit angka.")):Promise.reject(new Error("NIP BPS tidak boleh kosong.")),sa=({visible:c,onCancel:n,pegawai:l,jabatan:S,unitKerja:M,role:U,onFinish:D,type:i,title:N,form:t})=>{const d=async()=>{await t.validateFields();const e=Number(t.getFieldValue("angka_kredit_dasar"))||0,u=t.getFieldValue("jabatan_id"),{data:j}=await h.get(`/api/jabatans/${u}`),P=j.angka_kredit||0,p={"SD/sederajat":0,"SLTP/sederajat":0,"SLTA/sederajat":0,DI:0,DII:0,DIII:0,"S1/DIV/sederajat":0,S2:1,S3:1},b=t.getFieldValue("ijazah_terakhir");let x=0;b&&p.hasOwnProperty(b)&&(x=P*p[b]);const{fromDate:v,toDate:J}={fromDate:new Date(t.getFieldValue("bulan")[0]),toDate:new Date(t.getFieldValue("bulan")[1])},F=v.getMonth(),y=J.getMonth(),K=J.getFullYear()-v.getFullYear(),C=y-F+1+K*12,A=t.getFieldValue("predikat_id");let _=0;try{_=P*g[A-1].nilai*C/12}catch{}const E=e+x+_;t.setFieldValue("akumulasi_ak",Number(E).toFixed(4))},[g,f]=k.useState([]),V=async()=>{try{const{data:e}=await h.get("/api/predikats");f(e)}catch{console.error("Error when get predikat data")}};k.useEffect(()=>{V();async function e(){try{const{data:u}=await h.get(route("api.token.csrf"));t.setFieldValue("_token",u)}catch{console.log("error get token")}}e()},[]),k.useEffect(()=>{l&&(l.bulan_mulai?l.bulan=[m(l.bulan_mulai),m(l.bulan_selesai)]:l.bulan=[null,null],t.setFieldsValue(l))},[l]);const w=[{label:"Juru Muda/Ia",value:"Juru Muda/Ia"},{label:"Juru Muda Tingkat I/Ib",value:"Juru Muda Tingkat I/Ib"},{label:"Juru/Ic",value:"Juru/Ic"},{label:"Juru Tingkat I/Id",value:"Juru Tingkat I/Id"},{label:"Juru Pengatur Muda/IIa",value:"Juru Pengatur Muda/IIa"},{label:"Juru Pengatur Muda Tingkat I/IIb",value:"Juru Pengatur Muda Tingkat I/IIb"},{label:"Juru Pengatur/IIc",value:"Juru Pengatur/IIc"},{label:"Juru Pengatur Tingkat I/IId",value:"Juru Pengatur Tingkat I/IId"},{label:"Juru Penata Muda/IIIa",value:"Juru Penata Muda/IIIa"},{label:"Juru Penata Muda Tingkat I/IIIb",value:"Juru Penata Muda Tingkat I/IIIb"},{label:"Juru Penata/IIIc",value:"Juru Penata/IIIc"},{label:"Juru Penata Tingkat I/IIId",value:"Juru Penata Tingkat I/IIId"},{label:"Juru Pembina/IVa",value:"Juru Pembina/IVa"},{label:"Juru Pembina Tingkat I/IVb",value:"Juru Pembina Tingkat I/IVb"},{label:"Juru Pembina Tingkat Utama Muda/IVc",value:"Juru Pembina Tingkat Utama Muda/IVc"},{label:"Juru Pembina Tingkat Utama Madya/IVd",value:"Juru Pembina Tingkat Utama Madya/IVd"},{label:"Juru Pembina Tingkat Utama/IVe",value:"Juru Pembina Tingkat Utama/IVe"}];return a.jsx(T,{title:`${N} ${t.getFieldValue("nama")}  (${t.getFieldValue("nip_bps")})`,open:c,style:{top:20},onCancel:n,onOk:()=>t.submit(),width:600,okText:"Simpan",cancelText:"Batal",children:a.jsxs(r,{form:t,onFinish:D,layout:"vertical",wrapperCol:{span:24},autoComplete:"off",onKeyDown:e=>{e.code==="Enter"&&t.submit()},size:"large",children:[a.jsx(r.Item,{name:"id",hidden:!0,children:a.jsx(s,{})}),a.jsx(r.Item,{name:"_token",hidden:!0,children:a.jsx(s,{})}),a.jsx(r.Item,{name:"nip_bps",label:"NIP BPS",rules:[{required:!0,validator:L}],hidden:i==="edit",children:a.jsx(s,{placeholder:"Masukkan NIP lama contoh : 32002098",className:"border border-slate-400 rounded-md"})}),a.jsx(r.Item,{name:"nip",label:"NIP",rules:[{required:!0,validator:B}],hidden:i==="edit",children:a.jsx(s,{placeholder:"Masukkan NIP baru contoh : 198810232001041002",className:"border border-slate-400 rounded-md"})}),a.jsx(r.Item,{name:"nama",label:"Nama Pegawai",rules:[{required:!0}],hidden:i==="edit",children:a.jsx(s,{placeholder:"Nama Lengkap Tanpa Singkatan",className:"border border-slate-400 rounded-md"})}),a.jsx(r.Item,{name:"unit_kerja",label:"Satuan Kerja",rules:[{required:!0}],className:"focus:border-none",children:a.jsx(o,{placeholder:"Pilih Satuan Kerja",allowClear:!0,showSearch:!0,optionFilterProp:"label",options:M.map(e=>({label:e.nama,value:e.nama}))})}),a.jsx(r.Item,{name:"jabatan_id",label:"Jabatan",rules:[{required:!0}],className:"focus:border-none",children:a.jsx(o,{allowClear:!0,showSearch:!0,placeholder:"Pilih Jabatan Pegawai",optionFilterProp:"label",onChange:()=>{T.confirm({title:"Konfirmasi",content:"Mengubah jabatan pegawai akan mereset akumulasi angka kredit menjadi 0 (nol) !",footer:(e,{OkBtn:u,CancelBtn:j})=>a.jsx(a.Fragment,{children:a.jsx(u,{})})}),d()},options:S.map(e=>({label:e.nama,value:e.id}))})}),a.jsx(r.Item,{name:"pangkat_golongan_ruang",label:"Pangkat / Golongan Ruang",rules:[{required:!0}],children:a.jsx(o,{allowClear:!0,showSearch:!0,placeholder:"Penata Muda/IIIa",className:"border border-slate-400 rounded-md",options:w})}),a.jsx(r.Item,{name:"angka_kredit_konvensional",label:"Angka Kredit Konvensional",hidden:i==="edit",children:a.jsx(I,{className:"border w-[30%] border-slate-400 rounded-md"})}),a.jsx(r.Item,{name:"angka_kredit_integrasi",label:"Angka Kredit Integrasi",hidden:i==="edit",children:a.jsx(I,{className:"border w-[30%] border-slate-400 rounded-md"})}),a.jsx(r.Item,{name:"angka_kredit_dasar",label:"Angka Kredit Dasar / Terakhir",className:"focus:border-none",onChange:()=>d(),children:a.jsx(I,{className:"border border-slate-400 rounded-md"})}),a.jsx(r.Item,{name:"ijazah_terakhir",label:"Ijazah Terakhir",children:a.jsx(o,{onSelect:()=>d(),placeholder:"Pilih Ijazah yang Ditamatkan",options:[{label:"SD/sederajat",value:"SD/sederajat"},{label:"SLTP/sederajat",value:"SLTP/sederajat"},{label:"SLTA/sederajat",value:"SLTA/sederajat"},{label:"DI",value:"DI"},{label:"DII",value:"DII"},{label:"DIII",value:"DIII"},{label:"S1/DIV",value:"S1/DIV/sederajat"},{label:"S2",value:"S2"},{label:"S3",value:"S3"}]})}),a.jsx(r.Item,{name:"bulan",label:"Bulan Penilaian",rules:[{required:!0}],children:a.jsx(q,{picker:"month",minDate:m("2019-01-01"),format:"MMMM YYYY",maxDate:m(),onChange:d})}),a.jsx(r.Item,{name:"predikat_id",label:"Predikat Kinerja",className:"focus:border-none",children:a.jsx(o,{placeholder:"Pilih Predikat",allowClear:!0,showSearch:!0,optionFilterProp:"label",onChange:d,options:g.map(e=>({label:e.nama,value:e.id}))})}),a.jsx(r.Item,{name:"akumulasi_ak",label:"Akumulasi Angka Kredit",children:a.jsx(I,{readOnly:!0,className:"border border-slate-400 rounded-md"})})]})})};export{sa as default};