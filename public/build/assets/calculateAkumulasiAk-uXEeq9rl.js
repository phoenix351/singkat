const n=c=>{const{angka_kredit_integrasi:i,jabatan:r,predikat_kinerja:k,tambahan_ijazah:s}=c;if(!i||!r||!k)return"";const b=parseFloat(i);let a=0;if(r.includes("Ahli Pertama"))switch(k){case"Sangat Baik":a=18.75;break;case"Baik":a=12.5;break;case"Butuh Perbaikan":a=9.38;break;case"Kurang":a=6.25;break;case"Sangat Kurang":a=3.13;break;default:a=0;break}else if(r.includes("Ahli Muda"))switch(k){case"Sangat Baik":a=37.5;break;case"Baik":a=25;break;case"Butuh Perbaikan":a=28.13;break;case"Kurang":a=18.75;break;case"Sangat Kurang":a=6.25;break;default:a=0;break}else if(r.includes("Ahli Madya"))switch(k){case"Sangat Baik":a=56.25;break;case"Baik":a=37.5;break;case"Butuh Perbaikan":a=28.13;break;case"Kurang":a=18.75;break;case"Sangat Kurang":a=9.375;break;default:a=0;break}else if(r.includes("Ahli Utama"))switch(k){case"Sangat Baik":a=75;break;case"Baik":a=50;break;case"Butuh Perbaikan":a=37.5;break;case"Kurang":a=25;break;case"Sangat Kurang":a=12.5;break;default:a=0;break}else if(r.includes("Pemula"))switch(k){case"Sangat Baik":a=5.63;break;case"Baik":a=3.75;break;case"Butuh Perbaikan":a=2.81;break;case"Kurang":a=1.88;break;case"Sangat Kurang":a=.94;break;default:a=0;break}else if(r.includes("Terampil"))switch(k){case"Sangat Baik":a=7.5;break;case"Baik":a=5;break;case"Butuh Perbaikan":a=3.75;break;case"Kurang":a=2.5;break;case"Sangat Kurang":a=1.25;break;default:a=0;break}else if(r.includes("Mahir"))switch(k){case"Sangat Baik":a=18.75;break;case"Baik":a=12.5;break;case"Butuh Perbaikan":a=9.38;break;case"Kurang":a=6.25;break;case"Sangat Kurang":a=3.13;break;default:a=0;break}else if(r.includes("Penyelia"))switch(k){case"Sangat Baik":a=37.5;break;case"Baik":a=25;break;case"Butuh Perbaikan":a=18.75;break;case"Kurang":a=12.5;break;case"Sangat Kurang":a=6.25;break;default:a=0;break}let e=0;if(r.includes("Ahli Pertama"))switch(s){case"Sangat Baik":e=4.6875;break;case"Baik":e=3.125;break;case"Butuh Perbaikan":e=2.345;break;case"Kurang":e=1.5625;break;case"Sangat Kurang":e=.7825;break;default:e=0;break}else if(r.includes("Ahli Muda"))switch(s){case"Sangat Baik":e=9.375;break;case"Baik":e=6.25;break;case"Butuh Perbaikan":e=7.0325;break;case"Kurang":e=4.6875;break;case"Sangat Kurang":e=1.5625;break;default:e=0;break}else if(r.includes("Ahli Madya"))switch(s){case"Sangat Baik":e=14.0625;break;case"Baik":e=9.375;break;case"Butuh Perbaikan":e=7.0325;break;case"Kurang":e=4.6875;break;case"Sangat Kurang":e=2.34375;break;default:e=0;break}else if(r.includes("Ahli Utama"))switch(s){case"Sangat Baik":e=18.75;break;case"Baik":e=12.5;break;case"Butuh Perbaikan":e=9.375;break;case"Kurang":e=6.25;break;case"Sangat Kurang":e=3.125;break;default:e=0;break}else if(r.includes("Pemula"))switch(s){case"Sangat Baik":e=1.4075;break;case"Baik":e=.9375;break;case"Butuh Perbaikan":e=.7025;break;case"Kurang":e=.47;break;case"Sangat Kurang":e=.235;break;default:e=0;break}else if(r.includes("Terampil"))switch(s){case"Sangat Baik":e=1.875;break;case"Baik":e=1.25;break;case"Butuh Perbaikan":e=.9375;break;case"Kurang":e=.625;break;case"Sangat Kurang":e=.3125;break;default:e=0;break}else if(r.includes("Mahir"))switch(s){case"Sangat Baik":e=4.6875;break;case"Baik":e=3.125;break;case"Butuh Perbaikan":e=2.345;break;case"Kurang":e=1.5625;break;case"Sangat Kurang":e=.7825;break;default:e=0;break}else if(r.includes("Penyelia"))switch(s){case"Sangat Baik":e=9.375;break;case"Baik":e=6.25;break;case"Butuh Perbaikan":e=4.6875;break;case"Kurang":e=3.125;break;case"Sangat Kurang":e=1.5625;break;default:e=0;break}return(b+a+e).toFixed(3)};export{n as c};