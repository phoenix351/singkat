import{r as c,m as oe}from"./app-BGp6Cjwo.js";import{p as G,f as F,b as D,_ as T,o as K,e as B,g as Q,m as J,r as R,a as se,u as M,C as W,D as U,d as A,x as de,T as ue}from"./index-WmajGb9C.js";import{u as L,o as he,K as X,a as ge,R as me}from"./motion-B8OS_XYX.js";import{T as be,W as Y}from"./zoom-X00PKVfC.js";import{F as pe,u as Z}from"./PurePanel-BCMIoBXA.js";var fe=["prefixCls","className","style","checked","disabled","defaultChecked","type","title","onChange"],Ce=c.forwardRef(function(e,n){var t=e.prefixCls,a=t===void 0?"rc-checkbox":t,i=e.className,g=e.style,p=e.checked,r=e.disabled,l=e.defaultChecked,o=l===void 0?!1:l,u=e.type,f=u===void 0?"checkbox":u,E=e.title,d=e.onChange,w=G(e,fe),I=c.useRef(null),C=c.useRef(null),s=L(o,{value:p}),x=F(s,2),$=x[0],y=x[1];c.useImperativeHandle(n,function(){return{focus:function(h){var v;(v=I.current)===null||v===void 0||v.focus(h)},blur:function(){var h;(h=I.current)===null||h===void 0||h.blur()},input:I.current,nativeElement:C.current}});var S=D(a,i,T(T({},"".concat(a,"-checked"),$),"".concat(a,"-disabled"),r)),m=function(h){r||("checked"in e||y(h.target.checked),d==null||d({target:B(B({},e),{},{type:f,checked:h.target.checked}),stopPropagation:function(){h.stopPropagation()},preventDefault:function(){h.preventDefault()},nativeEvent:h.nativeEvent}))};return c.createElement("span",{className:S,title:E,style:g,ref:C},c.createElement("input",K({},w,{className:"".concat(a,"-input"),ref:I,onChange:m,disabled:r,checked:!!$,type:f})),c.createElement("span",{className:"".concat(a,"-inner")}))});const ve=e=>{const{checkboxCls:n}=e,t=`${n}-wrapper`;return[{[`${n}-group`]:Object.assign(Object.assign({},R(e)),{display:"inline-flex",flexWrap:"wrap",columnGap:e.marginXS,[`> ${e.antCls}-row`]:{flex:1}}),[t]:Object.assign(Object.assign({},R(e)),{display:"inline-flex",alignItems:"baseline",cursor:"pointer","&:after":{display:"inline-block",width:0,overflow:"hidden",content:"'\\a0'"},[`& + ${t}`]:{marginInlineStart:0},[`&${t}-in-form-item`]:{'input[type="checkbox"]':{width:14,height:14}}}),[n]:Object.assign(Object.assign({},R(e)),{position:"relative",whiteSpace:"nowrap",lineHeight:1,cursor:"pointer",borderRadius:e.borderRadiusSM,alignSelf:"center",[`${n}-input`]:{position:"absolute",inset:0,zIndex:1,cursor:"pointer",opacity:0,margin:0,[`&:focus-visible + ${n}-inner`]:Object.assign({},se(e))},[`${n}-inner`]:{boxSizing:"border-box",display:"block",width:e.checkboxSize,height:e.checkboxSize,direction:"ltr",backgroundColor:e.colorBgContainer,border:`${M(e.lineWidth)} ${e.lineType} ${e.colorBorder}`,borderRadius:e.borderRadiusSM,borderCollapse:"separate",transition:`all ${e.motionDurationSlow}`,"&:after":{boxSizing:"border-box",position:"absolute",top:"50%",insetInlineStart:"25%",display:"table",width:e.calc(e.checkboxSize).div(14).mul(5).equal(),height:e.calc(e.checkboxSize).div(14).mul(8).equal(),border:`${M(e.lineWidthBold)} solid ${e.colorWhite}`,borderTop:0,borderInlineStart:0,transform:"rotate(45deg) scale(0) translate(-50%,-50%)",opacity:0,content:'""',transition:`all ${e.motionDurationFast} ${e.motionEaseInBack}, opacity ${e.motionDurationFast}`}},"& + span":{paddingInlineStart:e.paddingXS,paddingInlineEnd:e.paddingXS}})},{[`
        ${t}:not(${t}-disabled),
        ${n}:not(${n}-disabled)
      `]:{[`&:hover ${n}-inner`]:{borderColor:e.colorPrimary}},[`${t}:not(${t}-disabled)`]:{[`&:hover ${n}-checked:not(${n}-disabled) ${n}-inner`]:{backgroundColor:e.colorPrimaryHover,borderColor:"transparent"},[`&:hover ${n}-checked:not(${n}-disabled):after`]:{borderColor:e.colorPrimaryHover}}},{[`${n}-checked`]:{[`${n}-inner`]:{backgroundColor:e.colorPrimary,borderColor:e.colorPrimary,"&:after":{opacity:1,transform:"rotate(45deg) scale(1) translate(-50%,-50%)",transition:`all ${e.motionDurationMid} ${e.motionEaseOutBack} ${e.motionDurationFast}`}}},[`
        ${t}-checked:not(${t}-disabled),
        ${n}-checked:not(${n}-disabled)
      `]:{[`&:hover ${n}-inner`]:{backgroundColor:e.colorPrimaryHover,borderColor:"transparent"}}},{[n]:{"&-indeterminate":{[`${n}-inner`]:{backgroundColor:e.colorBgContainer,borderColor:e.colorBorder,"&:after":{top:"50%",insetInlineStart:"50%",width:e.calc(e.fontSizeLG).div(2).equal(),height:e.calc(e.fontSizeLG).div(2).equal(),backgroundColor:e.colorPrimary,border:0,transform:"translate(-50%, -50%) scale(1)",opacity:1,content:'""'}}}}},{[`${t}-disabled`]:{cursor:"not-allowed"},[`${n}-disabled`]:{[`&, ${n}-input`]:{cursor:"not-allowed",pointerEvents:"none"},[`${n}-inner`]:{background:e.colorBgContainerDisabled,borderColor:e.colorBorder,"&:after":{borderColor:e.colorTextDisabled}},"&:after":{display:"none"},"& + span":{color:e.colorTextDisabled},[`&${n}-indeterminate ${n}-inner::after`]:{background:e.colorTextDisabled}}}]};function $e(e,n){const t=J(n,{checkboxCls:`.${e}`,checkboxSize:n.controlInteractiveSize});return[ve(t)]}const ee=Q("Checkbox",(e,n)=>{let{prefixCls:t}=n;return[$e(t,e)]}),ne=oe.createContext(null);var Se=function(e,n){var t={};for(var a in e)Object.prototype.hasOwnProperty.call(e,a)&&n.indexOf(a)<0&&(t[a]=e[a]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var i=0,a=Object.getOwnPropertySymbols(e);i<a.length;i++)n.indexOf(a[i])<0&&Object.prototype.propertyIsEnumerable.call(e,a[i])&&(t[a[i]]=e[a[i]]);return t};const xe=(e,n)=>{var t;const{prefixCls:a,className:i,rootClassName:g,children:p,indeterminate:r=!1,style:l,onMouseEnter:o,onMouseLeave:u,skipGroup:f=!1,disabled:E}=e,d=Se(e,["prefixCls","className","rootClassName","children","indeterminate","style","onMouseEnter","onMouseLeave","skipGroup","disabled"]),{getPrefixCls:w,direction:I,checkbox:C}=c.useContext(W),s=c.useContext(ne),{isFormItemInput:x}=c.useContext(pe),$=c.useContext(U),y=(t=(s==null?void 0:s.disabled)||E)!==null&&t!==void 0?t:$,S=c.useRef(d.value);c.useEffect(()=>{s==null||s.registerValue(d.value)},[]),c.useEffect(()=>{if(!f)return d.value!==S.current&&(s==null||s.cancelValue(S.current),s==null||s.registerValue(d.value),S.current=d.value),()=>s==null?void 0:s.cancelValue(d.value)},[d.value]);const m=w("checkbox",a),k=Z(m),[h,v,O]=ee(m,k),P=Object.assign({},d);s&&!f&&(P.onChange=function(){d.onChange&&d.onChange.apply(d,arguments),s.toggleOption&&s.toggleOption({label:p,value:d.value})},P.name=s.name,P.checked=s.value.includes(d.value));const j=D(`${m}-wrapper`,{[`${m}-rtl`]:I==="rtl",[`${m}-wrapper-checked`]:P.checked,[`${m}-wrapper-disabled`]:y,[`${m}-wrapper-in-form-item`]:x},C==null?void 0:C.className,i,g,O,k,v),H=D({[`${m}-indeterminate`]:r},be,v),q=r?"mixed":void 0;return h(c.createElement(Y,{component:"Checkbox",disabled:y},c.createElement("label",{className:j,style:Object.assign(Object.assign({},C==null?void 0:C.style),l),onMouseEnter:o,onMouseLeave:u},c.createElement(Ce,Object.assign({"aria-checked":q},P,{prefixCls:m,className:H,disabled:y,ref:n})),p!==void 0&&c.createElement("span",null,p))))},ae=c.forwardRef(xe);var ye=function(e,n){var t={};for(var a in e)Object.prototype.hasOwnProperty.call(e,a)&&n.indexOf(a)<0&&(t[a]=e[a]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var i=0,a=Object.getOwnPropertySymbols(e);i<a.length;i++)n.indexOf(a[i])<0&&Object.prototype.propertyIsEnumerable.call(e,a[i])&&(t[a[i]]=e[a[i]]);return t};const we=c.forwardRef((e,n)=>{const{defaultValue:t,children:a,options:i=[],prefixCls:g,className:p,rootClassName:r,style:l,onChange:o}=e,u=ye(e,["defaultValue","children","options","prefixCls","className","rootClassName","style","onChange"]),{getPrefixCls:f,direction:E}=c.useContext(W),[d,w]=c.useState(u.value||t||[]),[I,C]=c.useState([]);c.useEffect(()=>{"value"in u&&w(u.value||[])},[u.value]);const s=c.useMemo(()=>i.map(b=>typeof b=="string"||typeof b=="number"?{label:b,value:b}:b),[i]),x=b=>{C(N=>N.filter(z=>z!==b))},$=b=>{C(N=>[].concat(A(N),[b]))},y=b=>{const N=d.indexOf(b.value),z=A(d);N===-1?z.push(b.value):z.splice(N,1),"value"in u||w(z),o==null||o(z.filter(V=>I.includes(V)).sort((V,re)=>{const le=s.findIndex(_=>_.value===V),ce=s.findIndex(_=>_.value===re);return le-ce}))},S=f("checkbox",g),m=`${S}-group`,k=Z(S),[h,v,O]=ee(S,k),P=he(u,["value","disabled"]),j=i.length?s.map(b=>c.createElement(ae,{prefixCls:S,key:b.value.toString(),disabled:"disabled"in b?b.disabled:u.disabled,value:b.value,checked:d.includes(b.value),onChange:b.onChange,className:`${m}-item`,style:b.style,title:b.title,id:b.id,required:b.required},b.label)):a,H={toggleOption:y,value:d,disabled:u.disabled,name:u.name,registerValue:$,cancelValue:x},q=D(m,{[`${m}-rtl`]:E==="rtl"},p,r,O,k,v);return h(c.createElement("div",Object.assign({className:q,style:l},P,{ref:n}),c.createElement(ne.Provider,{value:H},j)))}),te=ae;te.Group=we;te.__ANT_CHECKBOX=!0;var Ie=["prefixCls","className","checked","defaultChecked","disabled","loadingIcon","checkedChildren","unCheckedChildren","onClick","onChange","onKeyDown"],ie=c.forwardRef(function(e,n){var t,a=e.prefixCls,i=a===void 0?"rc-switch":a,g=e.className,p=e.checked,r=e.defaultChecked,l=e.disabled,o=e.loadingIcon,u=e.checkedChildren,f=e.unCheckedChildren,E=e.onClick,d=e.onChange,w=e.onKeyDown,I=G(e,Ie),C=L(!1,{value:p,defaultValue:r}),s=F(C,2),x=s[0],$=s[1];function y(h,v){var O=x;return l||(O=h,$(O),d==null||d(O,v)),O}function S(h){h.which===X.LEFT?y(!1,h):h.which===X.RIGHT&&y(!0,h),w==null||w(h)}function m(h){var v=y(!x,h);E==null||E(v,h)}var k=D(i,g,(t={},T(t,"".concat(i,"-checked"),x),T(t,"".concat(i,"-disabled"),l),t));return c.createElement("button",K({},I,{type:"button",role:"switch","aria-checked":x,disabled:l,className:k,ref:n,onKeyDown:S,onClick:m}),o,c.createElement("span",{className:"".concat(i,"-inner")},c.createElement("span",{className:"".concat(i,"-inner-checked")},u),c.createElement("span",{className:"".concat(i,"-inner-unchecked")},f)))});ie.displayName="Switch";const ke=e=>{const{componentCls:n,trackHeightSM:t,trackPadding:a,trackMinWidthSM:i,innerMinMarginSM:g,innerMaxMarginSM:p,handleSizeSM:r,calc:l}=e,o=`${n}-inner`,u=M(l(r).add(l(a).mul(2)).equal()),f=M(l(p).mul(2).equal());return{[n]:{[`&${n}-small`]:{minWidth:i,height:t,lineHeight:M(t),[`${n}-inner`]:{paddingInlineStart:p,paddingInlineEnd:g,[`${o}-checked, ${o}-unchecked`]:{minHeight:t},[`${o}-checked`]:{marginInlineStart:`calc(-100% + ${u} - ${f})`,marginInlineEnd:`calc(100% - ${u} + ${f})`},[`${o}-unchecked`]:{marginTop:l(t).mul(-1).equal(),marginInlineStart:0,marginInlineEnd:0}},[`${n}-handle`]:{width:r,height:r},[`${n}-loading-icon`]:{top:l(l(r).sub(e.switchLoadingIconSize)).div(2).equal(),fontSize:e.switchLoadingIconSize},[`&${n}-checked`]:{[`${n}-inner`]:{paddingInlineStart:g,paddingInlineEnd:p,[`${o}-checked`]:{marginInlineStart:0,marginInlineEnd:0},[`${o}-unchecked`]:{marginInlineStart:`calc(100% - ${u} + ${f})`,marginInlineEnd:`calc(-100% + ${u} - ${f})`}},[`${n}-handle`]:{insetInlineStart:`calc(100% - ${M(l(r).add(a).equal())})`}},[`&:not(${n}-disabled):active`]:{[`&:not(${n}-checked) ${o}`]:{[`${o}-unchecked`]:{marginInlineStart:l(e.marginXXS).div(2).equal(),marginInlineEnd:l(e.marginXXS).mul(-1).div(2).equal()}},[`&${n}-checked ${o}`]:{[`${o}-checked`]:{marginInlineStart:l(e.marginXXS).mul(-1).div(2).equal(),marginInlineEnd:l(e.marginXXS).div(2).equal()}}}}}}},Ee=e=>{const{componentCls:n,handleSize:t,calc:a}=e;return{[n]:{[`${n}-loading-icon${e.iconCls}`]:{position:"relative",top:a(a(t).sub(e.fontSize)).div(2).equal(),color:e.switchLoadingIconColor,verticalAlign:"top"},[`&${n}-checked ${n}-loading-icon`]:{color:e.switchColor}}}},Oe=e=>{const{componentCls:n,trackPadding:t,handleBg:a,handleShadow:i,handleSize:g,calc:p}=e,r=`${n}-handle`;return{[n]:{[r]:{position:"absolute",top:t,insetInlineStart:t,width:g,height:g,transition:`all ${e.switchDuration} ease-in-out`,"&::before":{position:"absolute",top:0,insetInlineEnd:0,bottom:0,insetInlineStart:0,backgroundColor:a,borderRadius:p(g).div(2).equal(),boxShadow:i,transition:`all ${e.switchDuration} ease-in-out`,content:'""'}},[`&${n}-checked ${r}`]:{insetInlineStart:`calc(100% - ${M(p(g).add(t).equal())})`},[`&:not(${n}-disabled):active`]:{[`${r}::before`]:{insetInlineEnd:e.switchHandleActiveInset,insetInlineStart:0},[`&${n}-checked ${r}::before`]:{insetInlineEnd:0,insetInlineStart:e.switchHandleActiveInset}}}}},Me=e=>{const{componentCls:n,trackHeight:t,trackPadding:a,innerMinMargin:i,innerMaxMargin:g,handleSize:p,calc:r}=e,l=`${n}-inner`,o=M(r(p).add(r(a).mul(2)).equal()),u=M(r(g).mul(2).equal());return{[n]:{[l]:{display:"block",overflow:"hidden",borderRadius:100,height:"100%",paddingInlineStart:g,paddingInlineEnd:i,transition:`padding-inline-start ${e.switchDuration} ease-in-out, padding-inline-end ${e.switchDuration} ease-in-out`,[`${l}-checked, ${l}-unchecked`]:{display:"block",color:e.colorTextLightSolid,fontSize:e.fontSizeSM,transition:`margin-inline-start ${e.switchDuration} ease-in-out, margin-inline-end ${e.switchDuration} ease-in-out`,pointerEvents:"none",minHeight:t},[`${l}-checked`]:{marginInlineStart:`calc(-100% + ${o} - ${u})`,marginInlineEnd:`calc(100% - ${o} + ${u})`},[`${l}-unchecked`]:{marginTop:r(t).mul(-1).equal(),marginInlineStart:0,marginInlineEnd:0}},[`&${n}-checked ${l}`]:{paddingInlineStart:i,paddingInlineEnd:g,[`${l}-checked`]:{marginInlineStart:0,marginInlineEnd:0},[`${l}-unchecked`]:{marginInlineStart:`calc(100% - ${o} + ${u})`,marginInlineEnd:`calc(-100% + ${o} - ${u})`}},[`&:not(${n}-disabled):active`]:{[`&:not(${n}-checked) ${l}`]:{[`${l}-unchecked`]:{marginInlineStart:r(a).mul(2).equal(),marginInlineEnd:r(a).mul(-1).mul(2).equal()}},[`&${n}-checked ${l}`]:{[`${l}-checked`]:{marginInlineStart:r(a).mul(-1).mul(2).equal(),marginInlineEnd:r(a).mul(2).equal()}}}}}},Pe=e=>{const{componentCls:n,trackHeight:t,trackMinWidth:a}=e;return{[n]:Object.assign(Object.assign(Object.assign(Object.assign({},R(e)),{position:"relative",display:"inline-block",boxSizing:"border-box",minWidth:a,height:t,lineHeight:`${M(t)}`,verticalAlign:"middle",background:e.colorTextQuaternary,border:"0",borderRadius:100,cursor:"pointer",transition:`all ${e.motionDurationMid}`,userSelect:"none",[`&:hover:not(${n}-disabled)`]:{background:e.colorTextTertiary}}),de(e)),{[`&${n}-checked`]:{background:e.switchColor,[`&:hover:not(${n}-disabled)`]:{background:e.colorPrimaryHover}},[`&${n}-loading, &${n}-disabled`]:{cursor:"not-allowed",opacity:e.switchDisabledOpacity,"*":{boxShadow:"none",cursor:"not-allowed"}},[`&${n}-rtl`]:{direction:"rtl"}})}},Ne=e=>{const{fontSize:n,lineHeight:t,controlHeight:a,colorWhite:i}=e,g=n*t,p=a/2,r=2,l=g-r*2,o=p-r*2;return{trackHeight:g,trackHeightSM:p,trackMinWidth:l*2+r*4,trackMinWidthSM:o*2+r*2,trackPadding:r,handleBg:i,handleSize:l,handleSizeSM:o,handleShadow:`0 2px 4px 0 ${new ue("#00230b").setAlpha(.2).toRgbString()}`,innerMinMargin:l/2,innerMaxMargin:l+r+r*2,innerMinMarginSM:o/2,innerMaxMarginSM:o+r+r*2}},ze=Q("Switch",e=>{const n=J(e,{switchDuration:e.motionDurationMid,switchColor:e.colorPrimary,switchDisabledOpacity:e.opacityLoading,switchLoadingIconSize:e.calc(e.fontSizeIcon).mul(.75).equal(),switchLoadingIconColor:`rgba(0, 0, 0, ${e.opacityLoading})`,switchHandleActiveInset:"-30%"});return[Pe(n),Me(n),Oe(n),Ee(n),ke(n)]},Ne);var De=function(e,n){var t={};for(var a in e)Object.prototype.hasOwnProperty.call(e,a)&&n.indexOf(a)<0&&(t[a]=e[a]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var i=0,a=Object.getOwnPropertySymbols(e);i<a.length;i++)n.indexOf(a[i])<0&&Object.prototype.propertyIsEnumerable.call(e,a[i])&&(t[a[i]]=e[a[i]]);return t};const je=c.forwardRef((e,n)=>{const{prefixCls:t,size:a,disabled:i,loading:g,className:p,rootClassName:r,style:l,checked:o,value:u,defaultChecked:f,defaultValue:E,onChange:d}=e,w=De(e,["prefixCls","size","disabled","loading","className","rootClassName","style","checked","value","defaultChecked","defaultValue","onChange"]),[I,C]=L(!1,{value:o??u,defaultValue:f??E}),{getPrefixCls:s,direction:x,switch:$}=c.useContext(W),y=c.useContext(U),S=(i??y)||g,m=s("switch",t),k=c.createElement("div",{className:`${m}-handle`},g&&c.createElement(me,{className:`${m}-loading-icon`})),[h,v,O]=ze(m),P=ge(a),j=D($==null?void 0:$.className,{[`${m}-small`]:P==="small",[`${m}-loading`]:g,[`${m}-rtl`]:x==="rtl"},p,r,v,O),H=Object.assign(Object.assign({},$==null?void 0:$.style),l),q=function(){C(arguments.length<=0?void 0:arguments[0]),d==null||d.apply(void 0,arguments)};return h(c.createElement(Y,{component:"Switch"},c.createElement(ie,Object.assign({},w,{checked:I,onChange:q,prefixCls:m,className:j,style:H,disabled:S,ref:n,loadingIcon:k}))))}),He=je;He.__ANT_SWITCH=!0;export{Ce as C,He as S,te as a};
