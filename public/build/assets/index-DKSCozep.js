import{_ as H,a as ke,e as ae,f as G,k as ge,x as He,d as Re,b as J,n as B,g as De,m as Ge,r as We,C as pe,N as Ke,U as Ue,V as ze}from"./index-7Nr_NS3U.js";import{r as s,m as M,c as Ve}from"./app-DFnwsWrs.js";import{R as Be,r as Xe}from"./button-Bz9USzm7.js";import{a as Qe,R as Ye}from"./index-CxLqXYg-.js";import{p as qe,u as $e,R as Je,a as Ze}from"./PurePanel-CRgtzXFw.js";import{R as et}from"./ActionButton-CtM_w2vP.js";import{a as tt,K as xe,C as nt}from"./zoom-B0W9qZb7.js";var Ie=s.forwardRef(function(e,t){var a=e.prefixCls,n=e.style,o=e.className,r=e.duration,u=r===void 0?4.5:r,y=e.showProgress,l=e.pauseOnHover,i=l===void 0?!0:l,C=e.eventKey,g=e.content,v=e.closable,m=e.closeIcon,E=m===void 0?"x":m,d=e.props,p=e.onClick,A=e.onNoticeClose,R=e.times,O=e.hovering,P=s.useState(!1),S=H(P,2),h=S[0],$=S[1],f=s.useState(0),c=H(f,2),b=c[0],x=c[1],j=s.useState(0),F=H(j,2),w=F[0],X=F[1],k=O||h,L=u>0&&y,W=function(){A(C)},Q=function(N){(N.key==="Enter"||N.code==="Enter"||N.keyCode===tt.ENTER)&&W()};s.useEffect(function(){if(!k&&u>0){var T=Date.now()-w,N=setTimeout(function(){W()},u*1e3-w);return function(){i&&clearTimeout(N),X(Date.now()-T)}}},[u,k,R]),s.useEffect(function(){if(!k&&L&&(i||w===0)){var T=performance.now(),N,I=function ie(){cancelAnimationFrame(N),N=requestAnimationFrame(function(ce){var V=ce+w-T,K=Math.min(V/(u*1e3),1);x(K*100),K<1&&ie()})};return I(),function(){i&&cancelAnimationFrame(N)}}},[u,w,k,L,R]);var te=s.useMemo(function(){return ke(v)==="object"&&v!==null?v:v?{closeIcon:E}:{}},[v,E]),re=qe(te,!0),Y=100-(!b||b<0?0:b>100?100:b),D="".concat(a,"-notice");return s.createElement("div",ae({},d,{ref:t,className:G(D,o,ge({},"".concat(D,"-closable"),v)),style:n,onMouseEnter:function(N){var I;$(!0),d==null||(I=d.onMouseEnter)===null||I===void 0||I.call(d,N)},onMouseLeave:function(N){var I;$(!1),d==null||(I=d.onMouseLeave)===null||I===void 0||I.call(d,N)},onClick:p}),s.createElement("div",{className:"".concat(D,"-content")},g),v&&s.createElement("a",ae({tabIndex:0,className:"".concat(D,"-close"),onKeyDown:Q,"aria-label":"Close"},re,{onClick:function(N){N.preventDefault(),N.stopPropagation(),W()}}),te.closeIcon),L&&s.createElement("progress",{className:"".concat(D,"-progress"),max:"100",value:Y},Y+"%"))}),we=M.createContext({}),ot=function(t){var a=t.children,n=t.classNames;return M.createElement(we.Provider,{value:{classNames:n}},a)},Ne=8,be=3,Ee=16,at=function(t){var a={offset:Ne,threshold:be,gap:Ee};if(t&&ke(t)==="object"){var n,o,r;a.offset=(n=t.offset)!==null&&n!==void 0?n:Ne,a.threshold=(o=t.threshold)!==null&&o!==void 0?o:be,a.gap=(r=t.gap)!==null&&r!==void 0?r:Ee}return[!!t,a]},st=["className","style","classNames","styles"],rt=function(t){var a=t.configList,n=t.placement,o=t.prefixCls,r=t.className,u=t.style,y=t.motion,l=t.onAllNoticeRemoved,i=t.onNoticeClose,C=t.stack,g=s.useContext(we),v=g.classNames,m=s.useRef({}),E=s.useState(null),d=H(E,2),p=d[0],A=d[1],R=s.useState([]),O=H(R,2),P=O[0],S=O[1],h=a.map(function(k){return{config:k,key:String(k.key)}}),$=at(C),f=H($,2),c=f[0],b=f[1],x=b.offset,j=b.threshold,F=b.gap,w=c&&(P.length>0||h.length<=j),X=typeof y=="function"?y(n):y;return s.useEffect(function(){c&&P.length>1&&S(function(k){return k.filter(function(L){return h.some(function(W){var Q=W.key;return L===Q})})})},[P,h,c]),s.useEffect(function(){var k;if(c&&m.current[(k=h[h.length-1])===null||k===void 0?void 0:k.key]){var L;A(m.current[(L=h[h.length-1])===null||L===void 0?void 0:L.key])}},[h,c]),M.createElement(He,ae({key:n,className:G(o,"".concat(o,"-").concat(n),v==null?void 0:v.list,r,ge(ge({},"".concat(o,"-stack"),!!c),"".concat(o,"-stack-expanded"),w)),style:u,keys:h,motionAppear:!0},X,{onAllRemoved:function(){l(n)}}),function(k,L){var W=k.config,Q=k.className,te=k.style,re=k.index,Y=W,D=Y.key,T=Y.times,N=String(D),I=W,ie=I.className,ce=I.style,V=I.classNames,K=I.styles,_e=Re(I,st),le=h.findIndex(function(oe){return oe.key===N}),ne={};if(c){var q=h.length-1-(le>-1?le:re-1),Ce=n==="top"||n==="bottom"?"-50%":"0";if(q>0){var ue,fe,de;ne.height=w?(ue=m.current[N])===null||ue===void 0?void 0:ue.offsetHeight:p==null?void 0:p.offsetHeight;for(var he=0,ve=0;ve<q;ve++){var me;he+=((me=m.current[h[h.length-1-ve].key])===null||me===void 0?void 0:me.offsetHeight)+F}var Fe=(w?he:q*x)*(n.startsWith("top")?1:-1),Le=!w&&p!==null&&p!==void 0&&p.offsetWidth&&(fe=m.current[N])!==null&&fe!==void 0&&fe.offsetWidth?((p==null?void 0:p.offsetWidth)-x*2*(q<3?q:3))/((de=m.current[N])===null||de===void 0?void 0:de.offsetWidth):1;ne.transform="translate3d(".concat(Ce,", ").concat(Fe,"px, 0) scaleX(").concat(Le,")")}else ne.transform="translate3d(".concat(Ce,", 0, 0)")}return M.createElement("div",{ref:L,className:G("".concat(o,"-notice-wrapper"),Q,V==null?void 0:V.wrapper),style:J(J(J({},te),ne),K==null?void 0:K.wrapper),onMouseEnter:function(){return S(function(U){return U.includes(N)?U:[].concat(B(U),[N])})},onMouseLeave:function(){return S(function(U){return U.filter(function(Te){return Te!==N})})}},M.createElement(Ie,ae({},_e,{ref:function(U){le>-1?m.current[N]=U:delete m.current[N]},prefixCls:o,classNames:V,styles:K,className:G(ie,v==null?void 0:v.notice),style:ce,times:T,key:D,eventKey:D,onNoticeClose:i,hovering:c&&P.length>0})))})},it=s.forwardRef(function(e,t){var a=e.prefixCls,n=a===void 0?"rc-notification":a,o=e.container,r=e.motion,u=e.maxCount,y=e.className,l=e.style,i=e.onAllRemoved,C=e.stack,g=e.renderNotifications,v=s.useState([]),m=H(v,2),E=m[0],d=m[1],p=function(c){var b,x=E.find(function(j){return j.key===c});x==null||(b=x.onClose)===null||b===void 0||b.call(x),d(function(j){return j.filter(function(F){return F.key!==c})})};s.useImperativeHandle(t,function(){return{open:function(c){d(function(b){var x=B(b),j=x.findIndex(function(X){return X.key===c.key}),F=J({},c);if(j>=0){var w;F.times=(((w=b[j])===null||w===void 0?void 0:w.times)||0)+1,x[j]=F}else F.times=0,x.push(F);return u>0&&x.length>u&&(x=x.slice(-u)),x})},close:function(c){p(c)},destroy:function(){d([])}}});var A=s.useState({}),R=H(A,2),O=R[0],P=R[1];s.useEffect(function(){var f={};E.forEach(function(c){var b=c.placement,x=b===void 0?"topRight":b;x&&(f[x]=f[x]||[],f[x].push(c))}),Object.keys(O).forEach(function(c){f[c]=f[c]||[]}),P(f)},[E]);var S=function(c){P(function(b){var x=J({},b),j=x[c]||[];return j.length||delete x[c],x})},h=s.useRef(!1);if(s.useEffect(function(){Object.keys(O).length>0?h.current=!0:h.current&&(i==null||i(),h.current=!1)},[O]),!o)return null;var $=Object.keys(O);return Ve.createPortal(s.createElement(s.Fragment,null,$.map(function(f){var c=O[f],b=s.createElement(rt,{key:f,configList:c,placement:f,prefixCls:n,className:y==null?void 0:y(f),style:l==null?void 0:l(f),motion:r,onNoticeClose:p,onAllNoticeRemoved:S,stack:C});return g?g(b,{prefixCls:n,key:f}):b})),o)}),ct=["getContainer","motion","prefixCls","maxCount","className","style","onAllRemoved","stack","renderNotifications"],lt=function(){return document.body},Se=0;function ut(){for(var e={},t=arguments.length,a=new Array(t),n=0;n<t;n++)a[n]=arguments[n];return a.forEach(function(o){o&&Object.keys(o).forEach(function(r){var u=o[r];u!==void 0&&(e[r]=u)})}),e}function ft(){var e=arguments.length>0&&arguments[0]!==void 0?arguments[0]:{},t=e.getContainer,a=t===void 0?lt:t,n=e.motion,o=e.prefixCls,r=e.maxCount,u=e.className,y=e.style,l=e.onAllRemoved,i=e.stack,C=e.renderNotifications,g=Re(e,ct),v=s.useState(),m=H(v,2),E=m[0],d=m[1],p=s.useRef(),A=s.createElement(it,{container:E,ref:p,prefixCls:o,motion:n,maxCount:r,className:u,style:y,onAllRemoved:l,stack:i,renderNotifications:C}),R=s.useState([]),O=H(R,2),P=O[0],S=O[1],h=s.useMemo(function(){return{open:function(f){var c=ut(g,f);(c.key===null||c.key===void 0)&&(c.key="rc-notification-".concat(Se),Se+=1),S(function(b){return[].concat(B(b),[{type:"open",config:c}])})},close:function(f){S(function(c){return[].concat(B(c),[{type:"close",key:f}])})},destroy:function(){S(function(f){return[].concat(B(f),[{type:"destroy"}])})}}},[]);return s.useEffect(function(){d(a())}),s.useEffect(function(){p.current&&P.length&&(P.forEach(function($){switch($.type){case"open":p.current.open($.config);break;case"close":p.current.close($.key);break;case"destroy":p.current.destroy();break}}),S(function($){return $.filter(function(f){return!P.includes(f)})}))},[P]),[h,A]}const dt=e=>{const{componentCls:t,iconCls:a,boxShadow:n,colorText:o,colorSuccess:r,colorError:u,colorWarning:y,colorInfo:l,fontSizeLG:i,motionEaseInOutCirc:C,motionDurationSlow:g,marginXS:v,paddingXS:m,borderRadiusLG:E,zIndexPopup:d,contentPadding:p,contentBg:A}=e,R=`${t}-notice`,O=new xe("MessageMoveIn",{"0%":{padding:0,transform:"translateY(-100%)",opacity:0},"100%":{padding:m,transform:"translateY(0)",opacity:1}}),P=new xe("MessageMoveOut",{"0%":{maxHeight:e.height,padding:m,opacity:1},"100%":{maxHeight:0,padding:0,opacity:0}}),S={padding:m,textAlign:"center",[`${t}-custom-content > ${a}`]:{verticalAlign:"text-bottom",marginInlineEnd:v,fontSize:i},[`${R}-content`]:{display:"inline-block",padding:p,background:A,borderRadius:E,boxShadow:n,pointerEvents:"all"},[`${t}-success > ${a}`]:{color:r},[`${t}-error > ${a}`]:{color:u},[`${t}-warning > ${a}`]:{color:y},[`${t}-info > ${a},
      ${t}-loading > ${a}`]:{color:l}};return[{[t]:Object.assign(Object.assign({},We(e)),{color:o,position:"fixed",top:v,width:"100%",pointerEvents:"none",zIndex:d,[`${t}-move-up`]:{animationFillMode:"forwards"},[`
        ${t}-move-up-appear,
        ${t}-move-up-enter
      `]:{animationName:O,animationDuration:g,animationPlayState:"paused",animationTimingFunction:C},[`
        ${t}-move-up-appear${t}-move-up-appear-active,
        ${t}-move-up-enter${t}-move-up-enter-active
      `]:{animationPlayState:"running"},[`${t}-move-up-leave`]:{animationName:P,animationDuration:g,animationPlayState:"paused",animationTimingFunction:C},[`${t}-move-up-leave${t}-move-up-leave-active`]:{animationPlayState:"running"},"&-rtl":{direction:"rtl",span:{direction:"rtl"}}})},{[t]:{[`${R}-wrapper`]:Object.assign({},S)}},{[`${t}-notice-pure-panel`]:Object.assign(Object.assign({},S),{padding:0,textAlign:"start"})}]},vt=e=>({zIndexPopup:e.zIndexPopupBase+nt+10,contentBg:e.colorBgElevated,contentPadding:`${(e.controlHeightLG-e.fontSize*e.lineHeight)/2}px ${e.paddingSM}px`}),Me=De("Message",e=>{const t=Ge(e,{height:150});return[dt(t)]},vt);var mt=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(a[n[o]]=e[n[o]]);return a};const gt={info:s.createElement(Qe,null),success:s.createElement(Ye,null),error:s.createElement(Je,null),warning:s.createElement(et,null),loading:s.createElement(Be,null)},Ae=e=>{let{prefixCls:t,type:a,icon:n,children:o}=e;return s.createElement("div",{className:G(`${t}-custom-content`,`${t}-${a}`)},n||gt[a],s.createElement("span",null,o))},pt=e=>{const{prefixCls:t,className:a,type:n,icon:o,content:r}=e,u=mt(e,["prefixCls","className","type","icon","content"]),{getPrefixCls:y}=s.useContext(pe),l=t||y("message"),i=$e(l),[C,g,v]=Me(l,i);return C(s.createElement(Ie,Object.assign({},u,{prefixCls:l,className:G(a,g,`${l}-notice-pure-panel`,v,i),eventKey:"pure",duration:null,content:s.createElement(Ae,{prefixCls:l,type:n,icon:o},r)})))};function yt(e,t){return{motionName:t??`${e}-move-up`}}function ye(e){let t;const a=new Promise(o=>{t=e(()=>{o(!0)})}),n=()=>{t==null||t()};return n.then=(o,r)=>a.then(o,r),n.promise=a,n}var Ct=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(a[n[o]]=e[n[o]]);return a};const ht=8,xt=3,Nt=e=>{let{children:t,prefixCls:a}=e;const n=$e(a),[o,r,u]=Me(a,n);return o(s.createElement(ot,{classNames:{list:G(r,u,n)}},t))},bt=(e,t)=>{let{prefixCls:a,key:n}=t;return s.createElement(Nt,{prefixCls:a,key:n},e)},Et=s.forwardRef((e,t)=>{const{top:a,prefixCls:n,getContainer:o,maxCount:r,duration:u=xt,rtl:y,transitionName:l,onAllRemoved:i}=e,{getPrefixCls:C,getPopupContainer:g,message:v,direction:m}=s.useContext(pe),E=n||C("message"),d=()=>({left:"50%",transform:"translateX(-50%)",top:a??ht}),p=()=>G({[`${E}-rtl`]:y??m==="rtl"}),A=()=>yt(E,l),R=s.createElement("span",{className:`${E}-close-x`},s.createElement(Ze,{className:`${E}-close-icon`})),[O,P]=ft({prefixCls:E,style:d,className:p,motion:A,closable:!1,closeIcon:R,duration:u,getContainer:()=>(o==null?void 0:o())||(g==null?void 0:g())||document.body,maxCount:r,onAllRemoved:i,renderNotifications:bt});return s.useImperativeHandle(t,()=>Object.assign(Object.assign({},O),{prefixCls:E,message:v})),P});let Oe=0;function je(e){const t=s.useRef(null);return Ke(),[s.useMemo(()=>{const n=l=>{var i;(i=t.current)===null||i===void 0||i.close(l)},o=l=>{if(!t.current){const h=()=>{};return h.then=()=>{},h}const{open:i,prefixCls:C,message:g}=t.current,v=`${C}-notice`,{content:m,icon:E,type:d,key:p,className:A,style:R,onClose:O}=l,P=Ct(l,["content","icon","type","key","className","style","onClose"]);let S=p;return S==null&&(Oe+=1,S=`antd-message-${Oe}`),ye(h=>(i(Object.assign(Object.assign({},P),{key:S,content:s.createElement(Ae,{prefixCls:C,type:d,icon:E},m),placement:"top",className:G(d&&`${v}-${d}`,A,g==null?void 0:g.className),style:Object.assign(Object.assign({},g==null?void 0:g.style),R),onClose:()=>{O==null||O(),h()}})),()=>{n(S)}))},u={open:o,destroy:l=>{var i;l!==void 0?n(l):(i=t.current)===null||i===void 0||i.destroy()}};return["info","success","warning","error","loading"].forEach(l=>{const i=(C,g,v)=>{let m;C&&typeof C=="object"&&"content"in C?m=C:m={content:C};let E,d;typeof g=="function"?d=g:(E=g,d=v);const p=Object.assign(Object.assign({onClose:d,duration:E},m),{type:l});return o(p)};u[l]=i}),u},[]),s.createElement(Et,Object.assign({key:"message-holder"},e,{ref:t}))]}function St(e){return je(e)}const Ot=M.createContext({});let _=null,z=e=>e(),Z=[],ee={};function Pe(){const{getContainer:e,duration:t,rtl:a,maxCount:n,top:o}=ee,r=(e==null?void 0:e())||document.body;return{getContainer:()=>r,duration:t,rtl:a,maxCount:n,top:o}}const Pt=M.forwardRef((e,t)=>{const{messageConfig:a,sync:n}=e,{getPrefixCls:o}=s.useContext(pe),r=ee.prefixCls||o("message"),u=s.useContext(Ot),[y,l]=je(Object.assign(Object.assign(Object.assign({},a),{prefixCls:r}),u.message));return M.useImperativeHandle(t,()=>{const i=Object.assign({},y);return Object.keys(i).forEach(C=>{i[C]=function(){return n(),y[C].apply(y,arguments)}}),{instance:i,sync:n}}),l}),kt=M.forwardRef((e,t)=>{const[a,n]=M.useState(Pe),o=()=>{n(Pe)};M.useEffect(o,[]);const r=ze(),u=r.getRootPrefixCls(),y=r.getIconPrefixCls(),l=r.getTheme(),i=M.createElement(Pt,{ref:t,sync:o,messageConfig:a});return M.createElement(Ue,{prefixCls:u,iconPrefixCls:y,theme:l},r.holderRender?r.holderRender(i):i)});function se(){if(!_){const e=document.createDocumentFragment(),t={fragment:e};_=t,z(()=>{Xe(M.createElement(kt,{ref:a=>{const{instance:n,sync:o}=a||{};Promise.resolve().then(()=>{!t.instance&&n&&(t.instance=n,t.sync=o,se())})}}),e)});return}_.instance&&(Z.forEach(e=>{const{type:t,skipped:a}=e;if(!a)switch(t){case"open":{z(()=>{const n=_.instance.open(Object.assign(Object.assign({},ee),e.config));n==null||n.then(e.resolve),e.setCloseFn(n)});break}case"destroy":z(()=>{_==null||_.instance.destroy(e.key)});break;default:z(()=>{var n;const o=(n=_.instance)[t].apply(n,B(e.args));o==null||o.then(e.resolve),e.setCloseFn(o)})}}),Z=[])}function Rt(e){ee=Object.assign(Object.assign({},ee),e),z(()=>{var t;(t=_==null?void 0:_.sync)===null||t===void 0||t.call(_)})}function $t(e){const t=ye(a=>{let n;const o={type:"open",config:e,resolve:a,setCloseFn:r=>{n=r}};return Z.push(o),()=>{n?z(()=>{n()}):o.skipped=!0}});return se(),t}function It(e,t){const a=ye(n=>{let o;const r={type:e,args:t,resolve:n,setCloseFn:u=>{o=u}};return Z.push(r),()=>{o?z(()=>{o()}):r.skipped=!0}});return se(),a}const wt=e=>{Z.push({type:"destroy",key:e}),se()},Mt=["success","info","warning","error","loading"],At={open:$t,destroy:wt,config:Rt,useMessage:St,_InternalPanelDoNotUseOrYouWillBeFired:pt},jt=At;Mt.forEach(e=>{jt[e]=function(){for(var t=arguments.length,a=new Array(t),n=0;n<t;n++)a[n]=arguments[n];return It(e,a)}});export{jt as s};
