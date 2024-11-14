import{j as a,y as p}from"./app-DG4lkm6I.js";import{c as k}from"./calculateAkumulasiAk-uXEeq9rl.js";import{F as e}from"./index-bg-KSOeg.js";import{M as g}from"./index-CP26vVl7.js";import{I as o}from"./index-jsO2WCLV.js";import{S as n}from"./index-CdRAOwz7.js";import{T as s}from"./index-CTG7GsG4.js";import"./PurePanel-X1WZuywX.js";import"./index-BOoDnPea.js";import"./compact-item-CWVgPv4E.js";import"./zoom-CYFUwcOb.js";import"./row-B-yTj_Is.js";import"./index-DPovuAtR.js";import"./useLocale-Bn5sQBMJ.js";import"./ActionButton-BmFTjVmE.js";import"./button-B7yed1Qm.js";import"./index-CQGu2i8L.js";import"./TextArea-Q31wMMF0.js";import"./BaseInput-QhogEnYX.js";import"./index-C85wRqGF.js";const P={color:"#000"},j=(i,r)=>r?/^\d{18}$/.test(r)?Promise.resolve():Promise.reject(new Error("NIP harus terdiri dari 18 angka.")):Promise.reject(new Error("NIP tidak boleh kosong.")),x=(i,r)=>r?/^\d+$/.test(r)?Promise.resolve():Promise.reject(new Error("NIP BPS harus angka.")):Promise.reject(new Error("NIP BPS tidak boleh kosong.")),R=({visible:i,onCancel:r,jabatan:d,unitKerja:m,role:I})=>{const[t]=e.useForm(),u=(l,c)=>{const h=k(c);t.setFieldsValue({akumulasi_ak:h})},b=l=>{p.post("/kelola-pak",l),t.resetFields(),r()};return a.jsx(g,{title:"Tambah Pegawai",open:i,onCancel:r,style:{top:20},onOk:()=>t.submit(),width:600,okText:"Tambah",cancelText:"Batal",children:a.jsxs(e,{form:t,onValuesChange:u,name:"control-hooks",onFinish:b,style:{maxWidth:600},layout:"vertical",wrapperCol:{span:24},autoComplete:"off",size:"large",children:[a.jsx(e.Item,{name:"nip_bps",label:"NIP BPS",rules:[{required:!0,validator:x}],children:a.jsx(o,{placeholder:"Masukkan NIP BPS Pegawai",className:"border w-full border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"nip",label:"NIP",rules:[{required:!0,validator:j}],children:a.jsx(o,{placeholder:"Masukkan NIP Pegawai",className:"border w-full border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"nama",label:"Nama Pegawai",rules:[{required:!0,message:"Nama tidak boleh kosong."}],children:a.jsx(o,{placeholder:"Kevin Pandoh",className:"border border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"jabatan",label:"Jabatan",rules:[{required:!0}],className:"focus:border-none",children:a.jsx(n,{placeholder:"Pilih Jabatan Pegawai",showSearch:!0,optionFilterProp:"label",options:d.map(l=>({value:l.nama,label:l.nama}))})}),a.jsx(e.Item,{name:"unit_kerja",label:"Satuan Kerja",rules:[{required:!0}],className:"focus:border-none",children:a.jsx(n,{placeholder:"Pilih Satuan Kerja Pegawai",allowClear:!0,showSearch:!0,optionFilterProp:"label",options:m.map(l=>({value:l.nama,label:l.nama}))})}),a.jsx(e.Item,{name:"pangkat_golongan_ruang",label:"Pangkat / Golongan Ruang",rules:[{required:!0,message:"Pangkat / Golongan tidak boleh kosong."}],children:a.jsx(o,{placeholder:"Pembina Utama Muda / IVc",className:"border border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"angka_kredit_konvensional",label:"Angka Kredit Konvensional",children:a.jsx(s,{className:"border w-[30%] border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"angka_kredit_integrasi",label:"Angka Kredit Integrasi",children:a.jsx(s,{className:"border w-[30%] border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"predikat_kinerja",label:"Predikat Kinerja",className:"focus:border-none",children:a.jsx(n,{placeholder:"Pilih Predikat",allowClear:!0,optionFilterProp:"children",options:[{value:"Sangat Baik",label:"Sangat Baik"},{value:"Baik",label:"Baik"},{value:"Butuh Perbaikan",label:"Butuh Perbaikan"},{value:"Kurang",label:"Kurang"},{value:"Sangat Kurang",label:"Sangat Kurang"}]})}),a.jsx(e.Item,{name:"tambahan_ijazah",label:"25% Tambahan Ijazah",className:"focus:border-none",children:a.jsx(n,{placeholder:"Pilih Predikat Ijazah",allowClear:!0,optionFilterProp:"children",options:[{value:"Sangat Baik",label:"Sangat Baik"},{value:"Baik",label:"Baik"},{value:"Butuh Perbaikan",label:"Butuh Perbaikan"},{value:"Kurang",label:"Kurang"},{value:"Sangat Kurang",label:"Sangat Kurang"}]})}),a.jsx(e.Item,{name:"ijazah_terakhir",label:"Ijazah Terakhir",children:a.jsx(o,{placeholder:"",className:"border border-slate-400 rounded-md"})}),a.jsx(e.Item,{name:"akumulasi_ak",label:"Akumulasi Angka Kredit",children:a.jsx(o,{style:P,placeholder:"",className:"border border-slate-400 rounded-md",disabled:!0})})]})})};export{R as default};
