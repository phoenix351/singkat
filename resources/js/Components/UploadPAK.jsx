import React from 'react';
import { UploadOutlined } from '@ant-design/icons';
import { Button, message, Upload } from 'antd';

const UploadPAK = ({capaian_id}) => {
    const props = {
        beforeUpload: (file) => {
          // console.log({file});
          
          const isPNG = file.type === 'application/pdf';
          const giantFile = file.size>1024*1024*5;
          if (!isPNG) {
            message.error(`${file.name} bukan dokumen PDF`);
          }
          if(giantFile){
              message.error(`${file.name} memiliki size lebih dari 5 MB`);
          }
          return isPNG || Upload.LIST_IGNORE || giantFile;
        },
        onChange: (info) => {
          console.log(info.fileList,{capaian_id});
        },
      };
    return (
  <Upload {...props}>
    <Button icon={<UploadOutlined />}>Upload PDF only (maks. 5MB)</Button>
  </Upload>
);}
export default UploadPAK;