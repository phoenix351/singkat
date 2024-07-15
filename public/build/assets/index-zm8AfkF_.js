import{r as o,m as V}from"./app-BGp6Cjwo.js";import{h as He,e as J,p as we,f as oe,d as Fe,o as me,b as L,_ as Ce,C as pe,D as We,c as Te,g as nt,m as at,w as _e,i as De,l as Le}from"./index-WmajGb9C.js";import{F as ge,e as ot,u as Ve,N as rt,p as st}from"./PurePanel-BCMIoBXA.js";import{u as Me,c as lt,i as it}from"./index-B12D2l46.js";import{u as Be,o as Xe,c as Ue,a as Ee,N as ut,I as qe,e as Ke}from"./motion-B8OS_XYX.js";import{B as Ge,t as ct,r as Se}from"./BaseInput-BO8apeIw.js";import{u as Ye,g as je,a as ke,R as dt}from"./SearchOutlined-DBFYwasW.js";import{B as ft}from"./zoom-X00PKVfC.js";import{R as vt}from"./index-Fg_hPEis.js";var mt=["show"];function Ze(e,r){return o.useMemo(function(){var a={};r&&(a.show=He(r)==="object"&&r.formatter?r.formatter:!!r),a=J(J({},a),e);var t=a,n=t.show,i=we(t,mt);return J(J({},i),{},{show:!!n,showFormatter:typeof n=="function"?n:void 0,strategy:i.strategy||function(l){return l.length}})},[e,r])}var gt=["autoComplete","onChange","onFocus","onBlur","onPressEnter","onKeyDown","prefixCls","disabled","htmlSize","className","maxLength","suffix","showCount","count","type","classes","classNames","styles","onCompositionStart","onCompositionEnd"],pt=o.forwardRef(function(e,r){var a=e.autoComplete,t=e.onChange,n=e.onFocus,i=e.onBlur,l=e.onPressEnter,u=e.onKeyDown,S=e.prefixCls,m=S===void 0?"rc-input":S,g=e.disabled,y=e.htmlSize,$=e.className,M=e.maxLength,h=e.suffix,T=e.showCount,H=e.count,B=e.type,z=B===void 0?"text":B,c=e.classes,N=e.classNames,w=e.styles,A=e.onCompositionStart,E=e.onCompositionEnd,p=we(e,gt),v=o.useState(!1),F=oe(v,2),j=F[0],C=F[1],D=o.useRef(!1),x=o.useRef(null),K=o.useRef(null),G=function(O){x.current&&ct(x.current,O)},W=Be(e.defaultValue,{value:e.value}),Y=oe(W,2),q=Y[0],b=Y[1],s=q==null?"":String(q),d=o.useState(null),f=oe(d,2),_=f[0],R=f[1],I=Ze(H,T),ee=I.max||M,ue=I.strategy(s),ve=!!ee&&ue>ee;o.useImperativeHandle(r,function(){var k;return{focus:G,blur:function(){var X;(X=x.current)===null||X===void 0||X.blur()},setSelectionRange:function(X,fe,ce){var ne;(ne=x.current)===null||ne===void 0||ne.setSelectionRange(X,fe,ce)},select:function(){var X;(X=x.current)===null||X===void 0||X.select()},input:x.current,nativeElement:((k=K.current)===null||k===void 0?void 0:k.nativeElement)||x.current}}),o.useEffect(function(){C(function(k){return k&&g?!1:k})},[g]);var se=function(O,X,fe){var ce=X;if(!D.current&&I.exceedFormatter&&I.max&&I.strategy(X)>I.max){if(ce=I.exceedFormatter(X,{max:I.max}),X!==ce){var ne,ie;R([((ne=x.current)===null||ne===void 0?void 0:ne.selectionStart)||0,((ie=x.current)===null||ie===void 0?void 0:ie.selectionEnd)||0])}}else if(fe.source==="compositionEnd")return;b(ce),x.current&&Se(x.current,O,t,ce)};o.useEffect(function(){if(_){var k;(k=x.current)===null||k===void 0||k.setSelectionRange.apply(k,Fe(_))}},[_]);var he=function(O){se(O,O.target.value,{source:"change"})},Z=function(O){D.current=!1,se(O,O.currentTarget.value,{source:"compositionEnd"}),E==null||E(O)},re=function(O){l&&O.key==="Enter"&&l(O),u==null||u(O)},Q=function(O){C(!0),n==null||n(O)},le=function(O){C(!1),i==null||i(O)},ze=function(O){b(""),G(),x.current&&Se(x.current,O,t)},be=ve&&"".concat(m,"-out-of-range"),Oe=function(){var O=Xe(e,["prefixCls","onPressEnter","addonBefore","addonAfter","prefix","suffix","allowClear","defaultValue","showCount","count","classes","htmlSize","styles","classNames"]);return V.createElement("input",me({autoComplete:a},O,{onChange:he,onFocus:Q,onBlur:le,onKeyDown:re,className:L(m,Ce({},"".concat(m,"-disabled"),g),N==null?void 0:N.input),style:w==null?void 0:w.input,ref:x,size:y,type:z,onCompositionStart:function(fe){D.current=!0,A==null||A(fe)},onCompositionEnd:Z}))},Ie=function(){var O=Number(ee)>0;if(h||I.show){var X=I.showFormatter?I.showFormatter({value:s,count:ue,maxLength:ee}):"".concat(ue).concat(O?" / ".concat(ee):"");return V.createElement(V.Fragment,null,I.show&&V.createElement("span",{className:L("".concat(m,"-show-count-suffix"),Ce({},"".concat(m,"-show-count-has-suffix"),!!h),N==null?void 0:N.count),style:J({},w==null?void 0:w.count)},X),h)}return null};return V.createElement(Ge,me({},p,{prefixCls:m,className:L($,be),handleReset:ze,value:s,focused:j,triggerFocus:G,suffix:Ie(),disabled:g,classes:c,classNames:N,styles:w}),Oe())});const ht=e=>{const{getPrefixCls:r,direction:a}=o.useContext(pe),{prefixCls:t,className:n}=e,i=r("input-group",t),l=r("input"),[u,S]=Me(l),m=L(i,{[`${i}-lg`]:e.size==="large",[`${i}-sm`]:e.size==="small",[`${i}-compact`]:e.compact,[`${i}-rtl`]:a==="rtl"},S,n),g=o.useContext(ge),y=o.useMemo(()=>Object.assign(Object.assign({},g),{isFormItemInput:!1}),[g]);return u(o.createElement("span",{className:m,style:e.style,onMouseEnter:e.onMouseEnter,onMouseLeave:e.onMouseLeave,onFocus:e.onFocus,onBlur:e.onBlur},o.createElement(ge.Provider,{value:y},e.children)))},Qe=e=>{let r;return typeof e=="object"&&(e!=null&&e.clearIcon)?r=e:e&&(r={clearIcon:V.createElement(ot,null)}),r};function Je(e,r){const a=o.useRef([]),t=()=>{a.current.push(setTimeout(()=>{var n,i,l,u;!((n=e.current)===null||n===void 0)&&n.input&&((i=e.current)===null||i===void 0?void 0:i.input.getAttribute("type"))==="password"&&(!((l=e.current)===null||l===void 0)&&l.input.hasAttribute("value"))&&((u=e.current)===null||u===void 0||u.input.removeAttribute("value"))}))};return o.useEffect(()=>(r&&t(),()=>a.current.forEach(n=>{n&&clearTimeout(n)})),[]),t}function Ct(e){return!!(e.prefix||e.suffix||e.allowClear||e.showCount)}var xt=function(e,r){var a={};for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.indexOf(t)<0&&(a[t]=e[t]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var n=0,t=Object.getOwnPropertySymbols(e);n<t.length;n++)r.indexOf(t[n])<0&&Object.prototype.propertyIsEnumerable.call(e,t[n])&&(a[t[n]]=e[t[n]]);return a};function bt(e,r){if(!e)return;e.focus(r);const{cursor:a}=r||{};if(a){const t=e.value.length;switch(a){case"start":e.setSelectionRange(0,0);break;case"end":e.setSelectionRange(t,t);break;default:e.setSelectionRange(0,t)}}}const Re=o.forwardRef((e,r)=>{var a;const{prefixCls:t,bordered:n=!0,status:i,size:l,disabled:u,onBlur:S,onFocus:m,suffix:g,allowClear:y,addonAfter:$,addonBefore:M,className:h,style:T,styles:H,rootClassName:B,onChange:z,classNames:c,variant:N}=e,w=xt(e,["prefixCls","bordered","status","size","disabled","onBlur","onFocus","suffix","allowClear","addonAfter","addonBefore","className","style","styles","rootClassName","onChange","classNames","variant"]),{getPrefixCls:A,direction:E,input:p}=V.useContext(pe),v=A("input",t),F=o.useRef(null),j=Ve(v),[C,D,x]=Me(v,j),{compactSize:K,compactItemClassnames:G}=Ue(v,E),W=Ee(Q=>{var le;return(le=l??K)!==null&&le!==void 0?le:Q}),Y=V.useContext(We),q=u??Y,{status:b,hasFeedback:s,feedbackIcon:d}=o.useContext(ge),f=ke(b,i),_=Ct(e)||!!s;o.useRef(_);const R=Je(F,!0),I=Q=>{R(),S==null||S(Q)},ee=Q=>{R(),m==null||m(Q)},ue=Q=>{R(),z==null||z(Q)},ve=(s||g)&&V.createElement(V.Fragment,null,g,s&&d),se=Q=>Q&&V.createElement(ut,null,V.createElement(rt,{override:!0,status:!0},Q)),he=Qe(y??(p==null?void 0:p.allowClear)),[Z,re]=Ye(N,n);return C(V.createElement(pt,Object.assign({ref:Te(r,F),prefixCls:v,autoComplete:p==null?void 0:p.autoComplete},w,{disabled:q,onBlur:I,onFocus:ee,style:Object.assign(Object.assign({},p==null?void 0:p.style),T),styles:Object.assign(Object.assign({},p==null?void 0:p.styles),H),suffix:ve,allowClear:he,className:L(h,B,x,j,G,p==null?void 0:p.className),onChange:ue,addonBefore:se(M),addonAfter:se($),classNames:Object.assign(Object.assign(Object.assign({},c),p==null?void 0:p.classNames),{input:L({[`${v}-sm`]:W==="small",[`${v}-lg`]:W==="large",[`${v}-rtl`]:E==="rtl"},c==null?void 0:c.input,(a=p==null?void 0:p.classNames)===null||a===void 0?void 0:a.input,D),variant:L({[`${v}-${Z}`]:re},je(v,f)),affixWrapper:L({[`${v}-affix-wrapper-sm`]:W==="small",[`${v}-affix-wrapper-lg`]:W==="large",[`${v}-affix-wrapper-rtl`]:E==="rtl"},D),wrapper:L({[`${v}-group-rtl`]:E==="rtl"},D),groupWrapper:L({[`${v}-group-wrapper-sm`]:W==="small",[`${v}-group-wrapper-lg`]:W==="large",[`${v}-group-wrapper-rtl`]:E==="rtl",[`${v}-group-wrapper-${Z}`]:re},je(`${v}-group-wrapper`,f,s),D)})})))}),yt=e=>{const{componentCls:r,paddingXS:a}=e;return{[`${r}`]:{display:"inline-flex",alignItems:"center",flexWrap:"nowrap",columnGap:a,"&-rtl":{direction:"rtl"},[`${r}-input`]:{textAlign:"center",paddingInline:e.paddingXXS},[`&${r}-sm ${r}-input`]:{paddingInline:e.calc(e.paddingXXS).div(2).equal()},[`&${r}-lg ${r}-input`]:{paddingInline:e.paddingXS}}}},St=nt(["Input","OTP"],e=>{const r=at(e,lt(e));return[yt(r)]},it);var wt=function(e,r){var a={};for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.indexOf(t)<0&&(a[t]=e[t]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var n=0,t=Object.getOwnPropertySymbols(e);n<t.length;n++)r.indexOf(t[n])<0&&Object.prototype.propertyIsEnumerable.call(e,t[n])&&(a[t[n]]=e[t[n]]);return a};const Et=o.forwardRef((e,r)=>{const{value:a,onChange:t,onActiveChange:n,index:i,mask:l}=e,u=wt(e,["value","onChange","onActiveChange","index","mask"]),S=a&&typeof l=="string"?l:a,m=h=>{t(i,h.target.value)},g=o.useRef(null);o.useImperativeHandle(r,()=>g.current);const y=()=>{_e(()=>{var h;const T=(h=g.current)===null||h===void 0?void 0:h.input;document.activeElement===T&&T&&T.select()})},$=h=>{let{key:T}=h;T==="ArrowLeft"?n(i-1):T==="ArrowRight"&&n(i+1),y()},M=h=>{h.key==="Backspace"&&!a&&n(i-1),y()};return o.createElement(Re,Object.assign({},u,{ref:g,value:S,onInput:m,onFocus:y,onKeyDown:$,onKeyUp:M,onMouseDown:y,onMouseUp:y,type:l===!0?"password":"text"}))});var Rt=function(e,r){var a={};for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.indexOf(t)<0&&(a[t]=e[t]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var n=0,t=Object.getOwnPropertySymbols(e);n<t.length;n++)r.indexOf(t[n])<0&&Object.prototype.propertyIsEnumerable.call(e,t[n])&&(a[t[n]]=e[t[n]]);return a};function ye(e){return(e||"").split("")}const zt=o.forwardRef((e,r)=>{const{prefixCls:a,length:t=6,size:n,defaultValue:i,value:l,onChange:u,formatter:S,variant:m,disabled:g,status:y,autoFocus:$,mask:M}=e,h=Rt(e,["prefixCls","length","size","defaultValue","value","onChange","formatter","variant","disabled","status","autoFocus","mask"]),{getPrefixCls:T,direction:H}=o.useContext(pe),B=T("otp",a),z=st(h,{aria:!0,data:!0,attr:!0}),c=Ve(B),[N,w,A]=St(B,c),E=Ee(s=>n??s),p=o.useContext(ge),v=ke(p.status,y),F=o.useMemo(()=>Object.assign(Object.assign({},p),{status:v,hasFeedback:!1,feedbackIcon:null}),[p,v]),j=o.useRef(null),C=o.useRef({});o.useImperativeHandle(r,()=>({focus:()=>{var s;(s=C.current[0])===null||s===void 0||s.focus()},blur:()=>{var s;for(let d=0;d<t;d+=1)(s=C.current[d])===null||s===void 0||s.blur()},nativeElement:j.current}));const D=s=>S?S(s):s,[x,K]=o.useState(ye(D(i||"")));o.useEffect(()=>{l!==void 0&&K(ye(l))},[l]);const G=De(s=>{K(s),u&&s.length===t&&s.every(d=>d)&&s.some((d,f)=>x[f]!==d)&&u(s.join(""))}),W=De((s,d)=>{let f=Fe(x);for(let R=0;R<s;R+=1)f[R]||(f[R]="");d.length<=1?f[s]=d:f=f.slice(0,s).concat(ye(d)),f=f.slice(0,t);for(let R=f.length-1;R>=0&&!f[R];R-=1)f.pop();const _=D(f.map(R=>R||" ").join(""));return f=ye(_).map((R,I)=>R===" "&&!f[I]?f[I]:R),f}),Y=(s,d)=>{var f;const _=W(s,d),R=Math.min(s+d.length,t-1);R!==s&&((f=C.current[R])===null||f===void 0||f.focus()),G(_)},q=s=>{var d;(d=C.current[s])===null||d===void 0||d.focus()},b={variant:m,disabled:g,status:v,mask:M};return N(o.createElement("div",Object.assign({},z,{ref:j,className:L(B,{[`${B}-sm`]:E==="small",[`${B}-lg`]:E==="large",[`${B}-rtl`]:H==="rtl"},A,w)}),o.createElement(ge.Provider,{value:F},Array.from({length:t}).map((s,d)=>{const f=`otp-${d}`,_=x[d]||"";return o.createElement(Et,Object.assign({ref:R=>{C.current[d]=R},key:f,index:d,size:E,htmlSize:1,className:`${B}-input`,onChange:Y,value:_,onActiveChange:q,autoFocus:d===0&&$},b))}))))});var Ot={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M942.2 486.2Q889.47 375.11 816.7 305l-50.88 50.88C807.31 395.53 843.45 447.4 874.7 512 791.5 684.2 673.4 766 512 766q-72.67 0-133.87-22.38L323 798.75Q408 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 000-51.5zm-63.57-320.64L836 122.88a8 8 0 00-11.32 0L715.31 232.2Q624.86 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 000 51.5q56.69 119.4 136.5 191.41L112.48 835a8 8 0 000 11.31L155.17 889a8 8 0 0011.31 0l712.15-712.12a8 8 0 000-11.32zM149.3 512C232.6 339.8 350.7 258 512 258c54.54 0 104.13 9.36 149.12 28.39l-70.3 70.3a176 176 0 00-238.13 238.13l-83.42 83.42C223.1 637.49 183.3 582.28 149.3 512zm246.7 0a112.11 112.11 0 01146.2-106.69L401.31 546.2A112 112 0 01396 512z"}},{tag:"path",attrs:{d:"M508 624c-3.46 0-6.87-.16-10.25-.47l-52.82 52.82a176.09 176.09 0 00227.42-227.42l-52.82 52.82c.31 3.38.47 6.79.47 10.25a111.94 111.94 0 01-112 112z"}}]},name:"eye-invisible",theme:"outlined"},It=function(r,a){return o.createElement(qe,me({},r,{ref:a,icon:Ot}))},Pt=o.forwardRef(It),$t={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M942.2 486.2C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 000 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM512 766c-161.3 0-279.4-81.8-362.7-254C232.6 339.8 350.7 258 512 258c161.3 0 279.4 81.8 362.7 254C791.5 684.2 673.4 766 512 766zm-4-430c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm0 288c-61.9 0-112-50.1-112-112s50.1-112 112-112 112 50.1 112 112-50.1 112-112 112z"}}]},name:"eye",theme:"outlined"},Nt=function(r,a){return o.createElement(qe,me({},r,{ref:a,icon:$t}))},At=o.forwardRef(Nt),_t=function(e,r){var a={};for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.indexOf(t)<0&&(a[t]=e[t]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var n=0,t=Object.getOwnPropertySymbols(e);n<t.length;n++)r.indexOf(t[n])<0&&Object.prototype.propertyIsEnumerable.call(e,t[n])&&(a[t[n]]=e[t[n]]);return a};const jt=e=>e?o.createElement(At,null):o.createElement(Pt,null),Ft={click:"onClick",hover:"onMouseOver"},Tt=o.forwardRef((e,r)=>{const{disabled:a,action:t="click",visibilityToggle:n=!0,iconRender:i=jt}=e,l=typeof n=="object"&&n.visible!==void 0,[u,S]=o.useState(()=>l?n.visible:!1),m=o.useRef(null);o.useEffect(()=>{l&&S(n.visible)},[l,n]);const g=Je(m),y=()=>{a||(u&&g(),S(p=>{var v;const F=!p;return typeof n=="object"&&((v=n.onVisibleChange)===null||v===void 0||v.call(n,F)),F}))},$=p=>{const v=Ft[t]||"",F=i(u),j={[v]:y,className:`${p}-icon`,key:"passwordIcon",onMouseDown:C=>{C.preventDefault()},onMouseUp:C=>{C.preventDefault()}};return o.cloneElement(o.isValidElement(F)?F:o.createElement("span",null,F),j)},{className:M,prefixCls:h,inputPrefixCls:T,size:H}=e,B=_t(e,["className","prefixCls","inputPrefixCls","size"]),{getPrefixCls:z}=o.useContext(pe),c=z("input",T),N=z("input-password",h),w=n&&$(N),A=L(N,M,{[`${N}-${H}`]:!!H}),E=Object.assign(Object.assign({},Xe(B,["suffix","iconRender","visibilityToggle"])),{type:u?"text":"password",className:A,prefixCls:c,suffix:w});return H&&(E.size=H),o.createElement(Re,Object.assign({ref:Te(r,m)},E))});var Vt=function(e,r){var a={};for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.indexOf(t)<0&&(a[t]=e[t]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var n=0,t=Object.getOwnPropertySymbols(e);n<t.length;n++)r.indexOf(t[n])<0&&Object.prototype.propertyIsEnumerable.call(e,t[n])&&(a[t[n]]=e[t[n]]);return a};const Mt=o.forwardRef((e,r)=>{const{prefixCls:a,inputPrefixCls:t,className:n,size:i,suffix:l,enterButton:u=!1,addonAfter:S,loading:m,disabled:g,onSearch:y,onChange:$,onCompositionStart:M,onCompositionEnd:h}=e,T=Vt(e,["prefixCls","inputPrefixCls","className","size","suffix","enterButton","addonAfter","loading","disabled","onSearch","onChange","onCompositionStart","onCompositionEnd"]),{getPrefixCls:H,direction:B}=o.useContext(pe),z=o.useRef(!1),c=H("input-search",a),N=H("input",t),{compactSize:w}=Ue(c,B),A=Ee(b=>{var s;return(s=i??w)!==null&&s!==void 0?s:b}),E=o.useRef(null),p=b=>{b&&b.target&&b.type==="click"&&y&&y(b.target.value,b,{source:"clear"}),$&&$(b)},v=b=>{var s;document.activeElement===((s=E.current)===null||s===void 0?void 0:s.input)&&b.preventDefault()},F=b=>{var s,d;y&&y((d=(s=E.current)===null||s===void 0?void 0:s.input)===null||d===void 0?void 0:d.value,b,{source:"input"})},j=b=>{z.current||m||F(b)},C=typeof u=="boolean"?o.createElement(dt,null):null,D=`${c}-button`;let x;const K=u||{},G=K.type&&K.type.__ANT_BUTTON===!0;G||K.type==="button"?x=Ke(K,Object.assign({onMouseDown:v,onClick:b=>{var s,d;(d=(s=K==null?void 0:K.props)===null||s===void 0?void 0:s.onClick)===null||d===void 0||d.call(s,b),F(b)},key:"enterButton"},G?{className:D,size:A}:{})):x=o.createElement(ft,{className:D,type:u?"primary":void 0,size:A,disabled:g,key:"enterButton",onMouseDown:v,onClick:F,loading:m,icon:C},u),S&&(x=[x,Ke(S,{key:"addonAfter"})]);const W=L(c,{[`${c}-rtl`]:B==="rtl",[`${c}-${A}`]:!!A,[`${c}-with-button`]:!!u},n),Y=b=>{z.current=!0,M==null||M(b)},q=b=>{z.current=!1,h==null||h(b)};return o.createElement(Re,Object.assign({ref:Te(E,r),onPressEnter:j},T,{size:A,onCompositionStart:Y,onCompositionEnd:q,prefixCls:N,addonAfter:x,suffix:l,onChange:p,className:W,disabled:g}))});var Bt=`
  min-height:0 !important;
  max-height:none !important;
  height:0 !important;
  visibility:hidden !important;
  overflow:hidden !important;
  position:absolute !important;
  z-index:-1000 !important;
  top:0 !important;
  right:0 !important;
  pointer-events: none !important;
`,kt=["letter-spacing","line-height","padding-top","padding-bottom","font-family","font-weight","font-size","font-variant","text-rendering","text-transform","width","text-indent","padding-left","padding-right","border-width","box-sizing","word-break","white-space"],Pe={},te;function Dt(e){var r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,a=e.getAttribute("id")||e.getAttribute("data-reactid")||e.getAttribute("name");if(r&&Pe[a])return Pe[a];var t=window.getComputedStyle(e),n=t.getPropertyValue("box-sizing")||t.getPropertyValue("-moz-box-sizing")||t.getPropertyValue("-webkit-box-sizing"),i=parseFloat(t.getPropertyValue("padding-bottom"))+parseFloat(t.getPropertyValue("padding-top")),l=parseFloat(t.getPropertyValue("border-bottom-width"))+parseFloat(t.getPropertyValue("border-top-width")),u=kt.map(function(m){return"".concat(m,":").concat(t.getPropertyValue(m))}).join(";"),S={sizingStyle:u,paddingSize:i,borderSize:l,boxSizing:n};return r&&a&&(Pe[a]=S),S}function Lt(e){var r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:!1,a=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null,t=arguments.length>3&&arguments[3]!==void 0?arguments[3]:null;te||(te=document.createElement("textarea"),te.setAttribute("tab-index","-1"),te.setAttribute("aria-hidden","true"),document.body.appendChild(te)),e.getAttribute("wrap")?te.setAttribute("wrap",e.getAttribute("wrap")):te.removeAttribute("wrap");var n=Dt(e,r),i=n.paddingSize,l=n.borderSize,u=n.boxSizing,S=n.sizingStyle;te.setAttribute("style","".concat(S,";").concat(Bt)),te.value=e.value||e.placeholder||"";var m=void 0,g=void 0,y,$=te.scrollHeight;if(u==="border-box"?$+=l:u==="content-box"&&($-=i),a!==null||t!==null){te.value=" ";var M=te.scrollHeight-i;a!==null&&(m=M*a,u==="border-box"&&(m=m+i+l),$=Math.max(m,$)),t!==null&&(g=M*t,u==="border-box"&&(g=g+i+l),y=$>g?"":"hidden",$=Math.min(g,$))}var h={height:$,overflowY:y,resize:"none"};return m&&(h.minHeight=m),g&&(h.maxHeight=g),h}var Kt=["prefixCls","onPressEnter","defaultValue","value","autoSize","onResize","className","style","disabled","onChange","onInternalAutoSize"],$e=0,Ne=1,Ae=2,Ht=o.forwardRef(function(e,r){var a=e,t=a.prefixCls;a.onPressEnter;var n=a.defaultValue,i=a.value,l=a.autoSize,u=a.onResize,S=a.className,m=a.style,g=a.disabled,y=a.onChange;a.onInternalAutoSize;var $=we(a,Kt),M=Be(n,{value:i,postState:function(_){return _??""}}),h=oe(M,2),T=h[0],H=h[1],B=function(_){H(_.target.value),y==null||y(_)},z=o.useRef();o.useImperativeHandle(r,function(){return{textArea:z.current}});var c=o.useMemo(function(){return l&&He(l)==="object"?[l.minRows,l.maxRows]:[]},[l]),N=oe(c,2),w=N[0],A=N[1],E=!!l,p=function(){try{if(document.activeElement===z.current){var _=z.current,R=_.selectionStart,I=_.selectionEnd,ee=_.scrollTop;z.current.setSelectionRange(R,I),z.current.scrollTop=ee}}catch{}},v=o.useState(Ae),F=oe(v,2),j=F[0],C=F[1],D=o.useState(),x=oe(D,2),K=x[0],G=x[1],W=function(){C($e)};Le(function(){E&&W()},[i,w,A,E]),Le(function(){if(j===$e)C(Ne);else if(j===Ne){var f=Lt(z.current,!1,w,A);C(Ae),G(f)}else p()},[j]);var Y=o.useRef(),q=function(){_e.cancel(Y.current)},b=function(_){j===Ae&&(u==null||u(_),l&&(q(),Y.current=_e(function(){W()})))};o.useEffect(function(){return q},[]);var s=E?K:null,d=J(J({},m),s);return(j===$e||j===Ne)&&(d.overflowY="hidden",d.overflowX="hidden"),o.createElement(vt,{onResize:b,disabled:!(l||u)},o.createElement("textarea",me({},$,{ref:z,style:d,className:L(t,S,Ce({},"".concat(t,"-disabled"),g)),disabled:g,value:T,onChange:B})))}),Wt=["defaultValue","value","onFocus","onBlur","onChange","allowClear","maxLength","onCompositionStart","onCompositionEnd","suffix","prefixCls","showCount","count","className","style","disabled","hidden","classNames","styles","onResize","readOnly"],Xt=V.forwardRef(function(e,r){var a,t=e.defaultValue,n=e.value,i=e.onFocus,l=e.onBlur,u=e.onChange,S=e.allowClear,m=e.maxLength,g=e.onCompositionStart,y=e.onCompositionEnd,$=e.suffix,M=e.prefixCls,h=M===void 0?"rc-textarea":M,T=e.showCount,H=e.count,B=e.className,z=e.style,c=e.disabled,N=e.hidden,w=e.classNames,A=e.styles,E=e.onResize,p=e.readOnly,v=we(e,Wt),F=Be(t,{value:n,defaultValue:t}),j=oe(F,2),C=j[0],D=j[1],x=C==null?"":String(C),K=V.useState(!1),G=oe(K,2),W=G[0],Y=G[1],q=V.useRef(!1),b=V.useState(null),s=oe(b,2),d=s[0],f=s[1],_=o.useRef(null),R=o.useRef(null),I=function(){var P;return(P=R.current)===null||P===void 0?void 0:P.textArea},ee=function(){I().focus()};o.useImperativeHandle(r,function(){var U;return{resizableTextArea:R.current,focus:ee,blur:function(){I().blur()},nativeElement:((U=_.current)===null||U===void 0?void 0:U.nativeElement)||I()}}),o.useEffect(function(){Y(function(U){return!c&&U})},[c]);var ue=V.useState(null),ve=oe(ue,2),se=ve[0],he=ve[1];V.useEffect(function(){if(se){var U;(U=I()).setSelectionRange.apply(U,Fe(se))}},[se]);var Z=Ze(H,T),re=(a=Z.max)!==null&&a!==void 0?a:m,Q=Number(re)>0,le=Z.strategy(x),ze=!!re&&le>re,be=function(P,ae){var de=ae;!q.current&&Z.exceedFormatter&&Z.max&&Z.strategy(ae)>Z.max&&(de=Z.exceedFormatter(ae,{max:Z.max}),ae!==de&&he([I().selectionStart||0,I().selectionEnd||0])),D(de),Se(P.currentTarget,P,u,de)},Oe=function(P){q.current=!0,g==null||g(P)},Ie=function(P){q.current=!1,be(P,P.currentTarget.value),y==null||y(P)},k=function(P){be(P,P.target.value)},O=function(P){var ae=v.onPressEnter,de=v.onKeyDown;P.key==="Enter"&&ae&&ae(P),de==null||de(P)},X=function(P){Y(!0),i==null||i(P)},fe=function(P){Y(!1),l==null||l(P)},ce=function(P){D(""),ee(),Se(I(),P,u)},ne=$,ie;Z.show&&(Z.showFormatter?ie=Z.showFormatter({value:x,count:le,maxLength:re}):ie="".concat(le).concat(Q?" / ".concat(re):""),ne=V.createElement(V.Fragment,null,ne,V.createElement("span",{className:L("".concat(h,"-data-count"),w==null?void 0:w.count),style:A==null?void 0:A.count},ie)));var et=function(P){var ae;E==null||E(P),(ae=I())!==null&&ae!==void 0&&ae.style.height&&f(!0)},tt=!v.autoSize&&!T&&!S;return V.createElement(Ge,{ref:_,value:x,allowClear:S,handleReset:ce,suffix:ne,prefixCls:h,classNames:J(J({},w),{},{affixWrapper:L(w==null?void 0:w.affixWrapper,Ce(Ce({},"".concat(h,"-show-count"),T),"".concat(h,"-textarea-allow-clear"),S))}),disabled:c,focused:W,className:L(B,ze&&"".concat(h,"-out-of-range")),style:J(J({},z),d&&!tt?{height:"auto"}:{}),dataAttrs:{affixWrapper:{"data-count":typeof ie=="string"?ie:void 0}},hidden:N,readOnly:p},V.createElement(Ht,me({},v,{maxLength:m,onKeyDown:O,onChange:k,onFocus:X,onBlur:fe,onCompositionStart:Oe,onCompositionEnd:Ie,className:L(w==null?void 0:w.textarea),style:J(J({},A==null?void 0:A.textarea),{},{resize:z==null?void 0:z.resize}),disabled:c,prefixCls:h,onResize:et,ref:R,readOnly:p})))}),Ut=function(e,r){var a={};for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&r.indexOf(t)<0&&(a[t]=e[t]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var n=0,t=Object.getOwnPropertySymbols(e);n<t.length;n++)r.indexOf(t[n])<0&&Object.prototype.propertyIsEnumerable.call(e,t[n])&&(a[t[n]]=e[t[n]]);return a};const qt=o.forwardRef((e,r)=>{var a,t;const{prefixCls:n,bordered:i=!0,size:l,disabled:u,status:S,allowClear:m,classNames:g,rootClassName:y,className:$,style:M,styles:h,variant:T}=e,H=Ut(e,["prefixCls","bordered","size","disabled","status","allowClear","classNames","rootClassName","className","style","styles","variant"]),{getPrefixCls:B,direction:z,textArea:c}=o.useContext(pe),N=Ee(l),w=o.useContext(We),A=u??w,{status:E,hasFeedback:p,feedbackIcon:v}=o.useContext(ge),F=ke(E,S),j=o.useRef(null);o.useImperativeHandle(r,()=>{var b;return{resizableTextArea:(b=j.current)===null||b===void 0?void 0:b.resizableTextArea,focus:s=>{var d,f;bt((f=(d=j.current)===null||d===void 0?void 0:d.resizableTextArea)===null||f===void 0?void 0:f.textArea,s)},blur:()=>{var s;return(s=j.current)===null||s===void 0?void 0:s.blur()}}});const C=B("input",n),D=Ve(C),[x,K,G]=Me(C,D),[W,Y]=Ye(T,i),q=Qe(m??(c==null?void 0:c.allowClear));return x(o.createElement(Xt,Object.assign({autoComplete:c==null?void 0:c.autoComplete},H,{style:Object.assign(Object.assign({},c==null?void 0:c.style),M),styles:Object.assign(Object.assign({},c==null?void 0:c.styles),h),disabled:A,allowClear:q,className:L(G,D,$,y,c==null?void 0:c.className),classNames:Object.assign(Object.assign(Object.assign({},g),c==null?void 0:c.classNames),{textarea:L({[`${C}-sm`]:N==="small",[`${C}-lg`]:N==="large"},K,g==null?void 0:g.textarea,(a=c==null?void 0:c.classNames)===null||a===void 0?void 0:a.textarea),variant:L({[`${C}-${W}`]:Y},je(C,F)),affixWrapper:L(`${C}-textarea-affix-wrapper`,{[`${C}-affix-wrapper-rtl`]:z==="rtl",[`${C}-affix-wrapper-sm`]:N==="small",[`${C}-affix-wrapper-lg`]:N==="large",[`${C}-textarea-show-count`]:e.showCount||((t=e.count)===null||t===void 0?void 0:t.show)},K)}),prefixCls:C,suffix:p&&o.createElement("span",{className:`${C}-textarea-suffix`},v),ref:j})))}),xe=Re;xe.Group=ht;xe.Search=Mt;xe.TextArea=qt;xe.Password=Tt;xe.OTP=zt;export{xe as I};