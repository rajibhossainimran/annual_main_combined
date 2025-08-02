import React from 'react'
import AsyncSelect from 'react-select/async';

function RemarksTemplate({ changeData, type }) {
    const handleChangeRemarks = (item,select) => {
        if(select.action == "select-option") {
            changeData(item.data.remarks_details);
        }
    }
    const loadOptions = (inputValue, callback) => {
        axios.get(window.app_url+`/remarks-template?type=${type}&text=`+inputValue).then((res)=>{
            const data = res.data;

            let option=[];
            for (const iterator of data) {
                option.push({value:iterator.id, label:iterator.remarks_details, data:iterator})
            }

            callback(option);
        })
    };
  return (
    <div>
            <div className='mb-2'>Add Remarks From Template :</div>
            <div>
                <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangeRemarks} value={''} defaultOptions placeholder="Select saved remarks" />
            </div>
    </div>
  )
}

export default RemarksTemplate
