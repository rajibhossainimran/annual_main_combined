import axios from '../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'

import "react-datepicker/dist/react-datepicker.css";

export default function ViewApprove() {
  const [fy, setFy] = useState('')
  const [demandDate, setDemandDate] = useState()
  const [demadType, setDemandType] = useState()
  const [DemandNo, setDemandNo] = useState()
  const [demandPVMS, setDemandPVMS] = useState([])
  const [financialYears, setFinancialYears] = useState([])
  const [isFormSubmited, setIsFormSubmited] = useState(false)
  const [demandId, setDemandId] = useState()
  const [IsPublished, setIsPublished] = useState(0)
  const [DemandItemType, setDemandItemType] = useState()
  const [userApproval, setUserApproval] = useState()
  const [userInfo, setUserInfo] = useState('')
  const [submitPvmsData, setSubmitPvmsData] = useState([])
  const [viewMood, setViewMood] = useState('create')
  const [urlDemandId, setUrlDemandId] = useState()

  const handleSubmitApprove = () => {
    axios.post(window.app_url + '/querterly_demand/approve', {demand_id: urlDemandId}).then((res) => {
      window.location.href = '/querterly_demand'
    })
    
  }

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

    axios.get(window.app_url + '/querterly_demand/details/json/'+path_split[path_split.length-1]).then((res) => {
      const data = res.data
      
      setFy(data.financial_year.name)
      setDemandDate(moment(data.demand_date).format('D, MM Y'))
      setDemandNo(data.demand_no)
      setDemandType(data.demand_type)

      let pvms_data = []
      for (const element of data.querterly_demand_pvms) {
        pvms_data.push({
          pvms_no: element.pvms.pvms_id,
          nomenclature: element.pvms.nomenclature,
          au: element.pvms.unit_name.name,
          current_stock: null,
          avg_last_month: null,
          receieved_qty: null,
          annual_qty: element.annual_demand_pvms_unit_demand.dg_qty,
          req_qty: element.req_qty,
          remarks: element.remarks,
        })
      }
      
      setDemandPVMS(pvms_data)
      
    })

    axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
      setUserInfo(res.data);
      
      if (res.data.user_approval_role) {
        setUserApproval(res.data.user_approval_role);
      }
    })
    
  }, [])

  return (
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
                <th className='text-center'>Current Stock</th>
                <th className='text-center'>Avg. Last 3 Month</th>
                <th className='text-center'>Receieved Qty.</th>
                <th className='text-center'>Annual Qty. ({fy})</th>
                <th className='text-right width-150'>Qty. Req.<span className='text-danger'>*</span></th>
                <th className='text-right'>Rest Qty.</th>
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
                    {val.pvms_id}
                  </td>
                  <td className='text-center'>
                    {val.nomenclature}
                  </td>
                  <td className='text-center'>
                    {val.au}
                  </td>
                  <td className='text-center'>{val.current_stock}</td>
                  <td className='text-center'>{val.avg_last_month}</td>
                  <td className='text-center'>{val?.receieved_qty}</td>
                  <td className='text-center'>{val?.annual_qty}</td>
                  <td>
                    {val.req_qty}
                  </td>
                  <td className='text-center'>0</td>
                  <td>
                    {val.remarks}
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
                {userApproval?.role_key == 'mo' &&
                  <div className="position-relative custom-control custom-checkbox mb-2">
                    <input name="check" id="exampleCheck" type="checkbox" checked={IsPublished == 1} onChange={(e) => {
                      if (e.target.checked) {
                        setIsPublished(1)
                      } else {
                        setIsPublished(0)
                      }
                    }} className="custom-control-input" />
                    <label for="exampleCheck" class="custom-control-label font-weight-bold f16">Checked & Forward for OIC approval.</label>
                  </div>
                }
                {viewMood=='approval' && <button className='btn btn-success' disabled={isFormSubmited} onClick={() => handleSubmitApprove()}>{isFormSubmited ? 'Saving...' : userApproval?.role_key=='head_clark' ? 'Forword' : 'Approve'}</button>}
                
              </div>
            }
          </div>
        </div>
      </div>
  )
}

if (document.getElementById('react-querterly-demand-view-approve')) {
  createRoot(document.getElementById('react-querterly-demand-view-approve')).render(<ViewApprove />)
}
