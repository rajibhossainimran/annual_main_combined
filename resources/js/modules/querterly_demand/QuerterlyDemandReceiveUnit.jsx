import axios from '../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import moment from 'moment';

import "react-datepicker/dist/react-datepicker.css";

export default function QuerterlyDemandReceiveUnit() {
  const [fy, setFy] = useState('')
  const [demandDate, setDemandDate] = useState()
  const [demadType, setDemandType] = useState()
  const [DemandNo, setDemandNo] = useState()
  const [demandPVMS, setDemandPVMS] = useState([])
  const [isFormSubmited, setIsFormSubmited] = useState(false)
  const [demandId, setDemandId] = useState()
  const [IsPublished, setIsPublished] = useState(0)
  const [userApproval, setUserApproval] = useState()
  const [userInfo, setUserInfo] = useState('')
  const [viewMood, setViewMood] = useState('create')
  const [urlDemandId, setUrlDemandId] = useState()
  const [isReceived, setIsReceived] = useState()

  useEffect(() => {
    const pathname = window.location.pathname;

    if(pathname.includes('/querterly_demand/view')){
      setViewMood('view')
    }

    if(pathname.includes('/querterly_demand/approval')){
      setViewMood('approval')
    }

    const path_split = pathname.split('/');

    setUrlDemandId(path_split[path_split.length-1])

    axios.get(window.app_url + '/querterly_demand/receive/details/json/'+path_split[path_split.length-1]).then((res) => {
      const { querterly_demand, querterly_demand_receive_pvms, is_received } = res.data
      
      setFy(querterly_demand.financial_year.name)
      setDemandDate(moment(querterly_demand.demand_date).format('d, MMMM YYYY'))
      setDemandNo(querterly_demand.demand_no)
      setDemandType(querterly_demand.demand_type)
      setIsReceived(is_received)
      
      let items = []
      for (const querterly_demand_receive_pvms_single of querterly_demand_receive_pvms) {
        
        const { querterly_demand_pvms } = querterly_demand_receive_pvms_single;
        const { pvms } = querterly_demand_pvms;
        
        items.push({
          querterly_demand_receive_pvms: querterly_demand_receive_pvms_single.id,
          pvms_no: pvms.id,
          nomenclature: pvms.nomenclature,
          au: pvms.unit_name.name,
          issued_qty: querterly_demand_receive_pvms_single.issued_qty,
          received_qty: querterly_demand_receive_pvms_single.received_qty,
          wastage_qty: querterly_demand_receive_pvms_single.wastage_qty,
          remarks: querterly_demand_receive_pvms_single.remarks
        })
      }

      setDemandPVMS(items);
    })

    axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
      setUserInfo(res.data);
      
      if (res.data.user_approval_role) {
        setUserApproval(res.data.user_approval_role);
      }
    })
    
  }, [])

  const handleOnChangeReceivePvmsData = (index, target) => {
    let demandPVMSCopy = {...demandPVMS[index]};
    demandPVMS[index] = {...demandPVMSCopy, [target.name]: target.value}
    setDemandPVMS([...demandPVMS]);
  }

  const handleSubmit = (e) => {
    e.preventDefault()
    
    const requestData = {
      querterly_demand_receive_id: urlDemandId,
      demandPVMS
    }

    axios.post(window.app_url + '/querterly_demand/receive/store', requestData).then((res) => {
      console.log(res.data);
      
    })
    
    
  }

  return (
    <form onSubmit={handleSubmit}>
      <div className="row p-2">
        <div className="col-lg-12">
          <div className='row mb-3'>
            <div className='col-md-9'>
              <div className='row'>
                <div className='col-6 mb-2'>
                  <b>Financial Year:<span className='text-danger'>*</span> </b>
                  <div>{fy}</div>
                </div>
                <div className='col-6 mb-2'>

                  {!demandId && <>
                    <b>Demand Date: </b>
                    <div>
                      {demandDate}
                    </div>
                  </>
                  }
                </div>
                <div className='col-6 mb-2'>
                  <b>Demand No:<span className='text-danger'>*</span></b>
                  <div>{DemandNo}</div>
                </div>
                <div className='col-6 mb-2'>
                  <b>Demand Types:<span className='text-danger'>*</span> </b>
                  <div>{demadType}</div>
                </div>

              </div>
            </div>
          </div>

          <table className='table'>
            <thead>
              <tr>
                <th>Sl</th>
                <th>PVMS.No</th>
                <th className='text-center'>Nomenclature</th>
                <th className='text-center'>A/U</th>
                <th className='text-center'>Current<br/> Stock</th>
                <th className='text-center'>Deliverd Qty.</th>
                <th className='text-center'>Received Qty.</th>
                <th className='text-center'>Wastage Qty.</th>
                <th className='text-center'>Remarks</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {demandPVMS.map((val, key) => (
                <tr key={key}>
                  <td>
                    {key + 1}
                  </td>
                  <td>
                    {val.pvms_no}
                  </td>
                  <td className='text-center'>
                    {val.nomenclature}
                  </td>
                  <td className='text-center'>
                    {val.au}
                  </td>
                  <td className='text-right'>{val.current_stock}</td>
                  <td className='text-right'>{val?.issued_qty}</td>
                  <td className='text-right'>
                    <input type='number' name='received_qty' className='form-control' onChange={(e) => handleOnChangeReceivePvmsData(key, e.target)} value={val.received_qty==0 ? '' : val.received_qty} required readOnly={isReceived}/>
                  </td>
                  <td className='text-right'>
                    <input type='number' name='wastage_qty' className='form-control' onChange={(e) => handleOnChangeReceivePvmsData(key, e.target)} value={val.wastage_qty} required readOnly={isReceived}/>
                  </td>
                  <td>
                    <textarea name="remarks" onChange={(e) => handleOnChangeReceivePvmsData(key, e.target)} className='form-control' value={val.remarks} readOnly={isReceived}></textarea>
                  </td>
                  <td>
                    
                  </td>
                </tr>
              ))}
            </tbody>
          </table>


          {(demadType != 4 && demandPVMS.length == 0) ?
            <div className='text-center'>No Pvms Added</div>
            :
            <></>
          }

          <div className='row my-3'>

            

          </div>
          <div className='d-flex justify-content-end gap-2'>

            {(demandPVMS.length == 0) ?
              <div className='text-center'></div>
              :
              <div className='text-right'>
                
                {!isReceived && <button className='btn btn-success' disabled={isFormSubmited}>{isFormSubmited ? 'Saving...' : 'Submit'}</button>}
                
              </div>
            }
          </div>
        </div>
      </div>
      </form>
  )
}

if (document.getElementById('react-querterly-demand-receive-unit')) {
  createRoot(document.getElementById('react-querterly-demand-receive-unit')).render(<QuerterlyDemandReceiveUnit />)
}
