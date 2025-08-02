import axios from '../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import "react-datepicker/dist/react-datepicker.css";
import moment from 'moment';

import "react-datepicker/dist/react-datepicker.css";

export default function QuerterlyDemandDelivery() {
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

  useEffect(() => {
    const pathname = window.location.pathname;

    if (pathname.includes('/querterly_demand/view')) {
      setViewMood('view')
    }

    if (pathname.includes('/querterly_demand/approval')) {
      setViewMood('approval')
    }

    const path_split = pathname.split('/');

    setUrlDemandId(path_split[path_split.length - 1])

    axios.get(window.app_url + '/querterly_demand/details/delivery/json/' + path_split[path_split.length - 1]).then((res) => {
      const data = res.data
      console.log(data);
      const pvms_stock = data.pvms_stock

      setFy(data.querterly_demand.financial_year.name)
      setDemandDate(moment(data.demand_date).format('D, MM Y'))
      setDemandNo(data.querterly_demand.demand_no)
      setDemandType(data.querterly_demand.demand_type)

      let pvms_data = []
      for (const querterly_demand_pvms_single of data.querterly_demand_pvms) {
        const pvms = querterly_demand_pvms_single.pvms;
        const annual_demand_pvms_unit_demand = querterly_demand_pvms_single.annual_demand_pvms_unit_demand;

        let delivery_data = [];
        let receieved_qty = 0;
        for (const receive_pvms of querterly_demand_pvms_single.querterly_demand_receive_pvms) {
          receieved_qty+=receive_pvms.issued_qty;

          delivery_data.push({
            batch_id: receive_pvms.batch_id,
            delivery_qty:receive_pvms.issued_qty,
            batch_no:receive_pvms.batch_pvms.batch_no,
            expire_date: receive_pvms.batch_pvms.expire_date,
            querterly_demand_pvms_id:  receive_pvms.querterly_demand_pvms_id,
            exists: true
          })
        }

        if(delivery_data.length == 0){
          delivery_data.push({delivery_qty: null,
            batch_no: null,
            expire_date: null,
            querterly_demand_pvms_id: querterly_demand_pvms_single.id,
            exists: false,
            batch_id: null})
        }

        pvms_data.push({
          querterly_demand_pvms_id: querterly_demand_pvms_single.id,
          pvms_id: pvms.id,
          pvms_no: pvms.pvms_id,
          nomenclature: pvms.nomenclature,
          au: pvms.unit_name.name,
          current_stock: pvms_stock.find(i => i.id==querterly_demand_pvms_single.pvms_id).stock_qty,
          avg_last_month: null,
          receieved_qty: receieved_qty,
          annual_qty: annual_demand_pvms_unit_demand.dg_qty,
          req_qty: querterly_demand_pvms_single.req_qty,
          remarks: querterly_demand_pvms_single.remarks,
          delivery_data: delivery_data,
          batch_pvms: querterly_demand_pvms_single.batch_pvms
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

  const handleAddMoreDeliveryData = (index, querty_demand) => {

    setDemandPVMS((prev) => {
      let copy = { ...prev[index] }
      copy.delivery_data.push({
        delivery_qty: null,
        batch_no: null,
        expire_date: null,
        querterly_demand_pvms_id: querty_demand.querterly_demand_pvms_id,
        exists: false,
        batch_id: null
      })
      prev[index] = copy

      return [...prev]
    })
  }

  const handleOnChangeReceivePvmsData = (target, workorder_pvms_index, workorder_pvms_delivery_data_index) => {
    let delivery_data = { ...demandPVMS[workorder_pvms_index] }.delivery_data;
    delivery_data[workorder_pvms_delivery_data_index] = { ...delivery_data[workorder_pvms_delivery_data_index], [target.name]: target.value }
    demandPVMS[workorder_pvms_index] = { ...demandPVMS[workorder_pvms_index], delivery_data: delivery_data }
    setDemandPVMS([...demandPVMS]);
  }

  const handleSubmit = (e) => {
    e.preventDefault()

    const requestData = {
      querterly_demand_id: urlDemandId,
      demandPVMS
    }

    axios.post(window.app_url + '/querterly_demand/delivery/store', requestData).then((res) => {
      window.location.href = '/querterly_demand/delivery'
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
                <th className='text-center'>Current<br /> Stock</th>
                <th className='text-center'>Qty. Req.</th>
                <th className='text-center'>Receieved Qty.</th>
                <th className='text-center'>Annual Qty.({fy})</th>
                <th className='text-right'></th>
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
                    {val.pvms_no}
                  </td>
                  <td className='text-center'>
                    {val.nomenclature}
                  </td>
                  <td className='text-center'>
                    {val.au}
                  </td>
                  <td className='text-center'>{val.current_stock}</td>
                  <td className='text-center'>{val.req_qty}</td>
                  <td className='text-center'>{val?.receieved_qty}</td>
                  <td className='text-center'>{val?.annual_qty}</td>
                  <td>
                    <table>
                      <thead>
                        <tr>
                          <th>Batch</th>
                          <th>Qty</th>
                        </tr>
                      </thead>
                      <tbody>
                        {val.delivery_data.map((delivery_val, delivey_key) => (
                          <tr key={delivey_key}>
                            <td>
                              {delivery_val.exists ?
                                <div>{delivery_val.batch_no}, Qty {delivery_val.delivery_qty}, ({delivery_val.expire_date})</div>
                                :
                                <select name='batch_id' className='form-control' disabled={delivery_val.exists} value={delivery_val.batch_no} onChange={(e) => { handleOnChangeReceivePvmsData(e.target, key, delivey_key) }} required={delivery_val.delivery_qty || delivery_val.expire_date}>
                                  <option value="">Select Batch</option>
                                  {val.batch_pvms.map((batch_val, batch_key) => (
                                    <option value={batch_val.id} key={batch_key}>{batch_val.batch_no} - Qty {batch_val.available_quantity} - ({batch_val.expire_date})</option>
                                  ))}
                                </select>
                              }

                            </td>
                            <td>
                              {delivery_val.exists ?
                                <div>{delivery_val.delivery_qty}</div>
                                :
                                <input type='text' className='form-control' name='delivery_qty' autoComplete='off' value={delivery_val.delivery_qty} onChange={(e) => { handleOnChangeReceivePvmsData(e.target, key, delivey_key) }} required={delivery_val.batch_no || delivery_val.expire_date} />
                              }

                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                    <button type='button' className='btn btn-primary btn-sm pull-right' onClick={() => handleAddMoreDeliveryData(key, val)}>Add More</button>
                  </td>
                  <td className='text-center'>{val.req_qty-val.receieved_qty}</td>
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
                <button className='btn btn-success' disabled={isFormSubmited}>{isFormSubmited ? 'Saving...' : 'Submit'}</button>

              </div>
            }
          </div>
        </div>
      </div>
    </form>
  )
}

if (document.getElementById('react-querterly-demand-receive')) {
  createRoot(document.getElementById('react-querterly-demand-receive')).render(<QuerterlyDemandDelivery />)
}
