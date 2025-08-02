import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import Swal from 'sweetalert2'
import DatePicker from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";

export default function CreateEditQuerterlyDemand() {
  const [type, setType] = useState()
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
  const [viewMood, setViewMood] = useState('create')
  const [urlDemandId, setUrlDemandId] = useState()
  const [annualDemandId, setAnnualDemandId] = useState()

  const loadOptions = (inputValue, callback) => {
    axios.get(window.app_url + '/settings/pvms/search?keyword=' + inputValue).then((res) => {
      const data = res.data;

      let option = [];
      for (const iterator of data) {
        option.push({ value: iterator.id, label: iterator.pvms_id + ' - ' + iterator.nomenclature + ' - ' + (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data: iterator })
      }

      callback(option);
    })
  };

  const pvmsChange = (value, item_id) => {
    const pvms_exists = demandPVMS.find(i => i.id == item_id)

    if (pvms_exists) {
      setDemandPVMS((prev) => {
        let copy = [...prev]
        let findPvmsIndex = copy.findIndex(item => item.id == value.id);
        if (findPvmsIndex > -1) {
          copy[findPvmsIndex].qty = parseInt(copy[findPvmsIndex].qty) + 1;
        }
        return copy
      })

    } else {
      const new_demand_pvms = {
        ...value.annual_demand_unit_pvms,
        stock: value.pvms_stock,
        receieved_qty: value.receieved_qty,
        qty: null,
        remarks: '',
        demandPvmsId: null
      }


      setDemandPVMS((prev) => {
        let copy = [...prev]
        copy.push(new_demand_pvms)

        return copy
      })
    }
  }

  const handleChangeFinancialYear = (value) => {
    setFy(value)

    axios.get(window.app_url + '/settings/pvms/search-annual-demand?pvms_id=1' + '&fy=' + value + '&sub_org_id=' + userInfo?.sub_org_id).then((res) => {
      if (res.data.annual_demand_unit_pvms) {
        const pvms_stock = res.data.pvms_stock;
        
        const annual_demand_unit_pvms = res.data.annual_demand_unit_pvms;
        setAnnualDemandId(annual_demand_unit_pvms[0].annual_demand_unit.annual_demand_id)
        
        let items = []
        for (const element of annual_demand_unit_pvms) {
          const p_v_m_s = element.annual_demand_pvms.p_v_m_s
          items.push({
            annual_demand_pvms_unit_demand_id: element.id,
            pvms_primary_id: p_v_m_s.id,
            pvms_id: p_v_m_s.pvms_id,
            nomenclature: p_v_m_s.nomenclature,
            au: p_v_m_s.unit_name.name,
            current_stock: pvms_stock.find(i => i.id==p_v_m_s.id).stock_qty,
            avg_last_3_month: 0,
            receieved_qty: 0,
            annual_qty: element.dg_qty,
            request_qty: 0,
            remarks: ''
          })
        }
        
        setDemandPVMS(items);
        
      } else {
        Swal.fire({
          icon: 'error',
          // title: 'Oops...',
          text: `PVMS is not listed in annual demand!`,
          // footer: '<a href="">Why do I have this issue?</a>'
        })
      }

    }).catch((err) => {
      Swal.fire({
        icon: 'error',
        // title: 'Oops...',
        text: err.response.data.message,
        // footer: '<a href="">Why do I have this issue?</a>'
      })
    })
  }

  const handleChangePvms = (value) => {
    if (!fy) {
      Swal.fire({
        icon: 'error',
        // title: 'Oops...',
        text: `Select Financial year first!`,
        // footer: '<a href="">Why do I have this issue?</a>'
      })
    }
    axios.get(window.app_url + '/settings/pvms/search-annual-demand?pvms_id=' + value.value + '&fy=' + fy + '&sub_org_id=' + userInfo?.sub_org_id).then((res) => {
      if (res.data.annual_demand_unit_pvms) {
        pvmsChange(res.data, res.data.annual_demand_unit_pvms.id);
      } else {
        Swal.fire({
          icon: 'error',
          // title: 'Oops...',
          text: `PVMS is not listed in annual demand!`,
          // footer: '<a href="">Why do I have this issue?</a>'
        })
      }

    }).catch((err) => {
      Swal.fire({
        icon: 'error',
        // title: 'Oops...',
        text: err.response.data.message,
        // footer: '<a href="">Why do I have this issue?</a>'
      })
    })

  }

  const handleDeletePVMS = (index) => {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-success ml-2',
        cancelButton: 'btn btn-danger mr-2'
      },
      buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
      title: 'Are you sure?',
      text: "You want to delete this row!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'No, cancel!',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {

        setDemandPVMS((prev) => {
          let copy = [...prev]

          return copy.filter((val, key) => {
            if (index != key) {
              return true;
            }
          })
        })

        swalWithBootstrapButtons.fire(
          'Deleted!',
          'Row has been deleted.',
          'success'
        )
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        // swalWithBootstrapButtons.fire(
        //   'Cancelled',
        //   'Your imaginary file is safe :)',
        //   'error'
        // )
      }
    })
  }

  const handleDemandPVMSValueChange = (e, index, pvms_value) => {
      setDemandPVMS((prev) => {
        let copy = [...prev]

        copy[index] = { ...copy[index], [e.name]: e.value };

        return copy
      })
  }

  const handleSubmitApprove = () => {
    axios.post(window.app_url + '/querterly_demand/approve', {demand_id: urlDemandId}).then((res) => {
      window.location.href = '/querterly_demand'
    })
    
  }

  const handleDemandPVMSSubmit = (e) => {
    e.preventDefault();

    const request_data = {
      fy,
      demand_date: demandDate,
      demand_type: demadType,
      demand_no: DemandNo,
      annual_demand_id: annualDemandId,
      pvms: demandPVMS
    }

    if(urlDemandId){
      axios.put(window.app_url + `/querterly_demand/${urlDemandId}/update`, request_data).then((res) => {
        console.log(res.data);
        if(viewMood=='approve'){
          if(userApproval?.role_key == 'mo'){
            if(IsPublished){
              handleSubmitApprove();
            }else{
              window.location.href = '/querterly_demand'
            }
          }else{
            handleSubmitApprove();
          }
          
        }else{
          window.location.href = '/querterly_demand'
        }
        
      })
    }else{
      axios.post(window.app_url + '/querterly_demand', request_data).then((res) => {
        console.log(res);
        window.location.href = '/querterly_demand'
      })
    }

    
    
  }

  const getDemandDetails = (querterly_demand_id) => {
    axios.get(window.app_url + '/querterly_demand/details/json/'+querterly_demand_id).then((res) => {

      const querterly_demand = res.data.querterly_demand

      setDemandDate(new Date(querterly_demand.demand_date))
      setDemandNo(querterly_demand.demand_no)
      setDemandType(querterly_demand.demand_type)
      setFy(querterly_demand.financial_year)

      if (res.data.annual_demand_unit_pvms) {
        const pvms_stock = res.data.pvms_stock;
        
        const annual_demand_unit_pvms = res.data.annual_demand_unit_pvms;
        setAnnualDemandId(annual_demand_unit_pvms[0].annual_demand_unit.annual_demand_id)
        
        let items = []
        for (const element of annual_demand_unit_pvms) {
          const p_v_m_s = element.annual_demand_pvms.p_v_m_s
          items.push({
            querterly_demand_pvms_id: element.querterly_demand_pvms ? element.querterly_demand_pvms.id : null,
            annual_demand_pvms_unit_demand_id: element.id,
            pvms_primary_id: p_v_m_s.id,
            pvms_id: p_v_m_s.pvms_id,
            nomenclature: p_v_m_s.nomenclature,
            au: p_v_m_s.unit_name.name,
            current_stock: pvms_stock.find(i => i.id==p_v_m_s.id).stock_qty,
            avg_last_3_month: 0,
            receieved_qty: 0,
            annual_qty: element.dg_qty,
            request_qty: element.querterly_demand_pvms ? element.querterly_demand_pvms.req_qty : 0,
            remarks: ''
          })
        }
        
        setDemandPVMS(items);
        
      } else {
        Swal.fire({
          icon: 'error',
          // title: 'Oops...',
          text: `PVMS is not listed in annual demand!`,
          // footer: '<a href="">Why do I have this issue?</a>'
        })
      }

    }).catch((err) => {
      Swal.fire({
        icon: 'error',
        // title: 'Oops...',
        text: err.response.data.message,
        // footer: '<a href="">Why do I have this issue?</a>'
      })
    })
  }

  const buttonName = () => {
    switch (viewMood) {
      case 'create':
        return 'Create'
      case 'approve':
        if(userApproval?.role_key == 'mo' && IsPublished){
          return 'Forword'
        } else if(userApproval?.role_key == 'mo'){
          return 'Save'
        } else{
          return 'Approve'
        }
        
      case 'edit':
        return 'Edit'
  
    }
  }

  useEffect(() => {
    const pathname = window.location.pathname;
    if(pathname=='/querterly_demand/create'){
      setViewMood('create')
    }else{
      if(pathname.includes('/querterly_demand/view')){
        setViewMood('view')
      }

      if(pathname.includes('/querterly_demand/approval')){
        setViewMood('approve')
      }

      if(pathname.includes('/querterly_demand/edit')){
        setViewMood('edit')
      }

      const path_split = pathname.split('/');

      setUrlDemandId(path_split[path_split.length-1])
      
      getDemandDetails(path_split[path_split.length-1]);
    }

    axios.get(window.app_url + '/settings/financial-years/api').then((res) => {
      setFinancialYears(res.data)
    })

    axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
      setUserInfo(res.data);

      if (res.data.user_approval_role) {
        setUserApproval(res.data.user_approval_role);
      }
    })
  }, [])

  return (
    <form onSubmit={handleDemandPVMSSubmit} className='p-2'>
      <div className="row">
        <div className="col-lg-12">
          <div className='row mb-3'>
            <div className='col-md-9'>
              <div className='row'>
                <div className='col-6 mb-2'>
                  <b>Financial Year:<span className='text-danger'>*</span> </b>
                  <select onChange={(e) => handleChangeFinancialYear(e.target.value)} value={fy} className='form-control' required={(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2)} disabled={window.demand_id} readOnly={viewMood=='view'}>
                    <option value="">Select</option>
                    {financialYears.map((val, key) => (
                      <option key={key} value={val.id}>{val.name}</option>
                    ))}
                  </select>
                </div>
                <div className='col-6 mb-2'>

                  {!demandId && <>
                    <b>Demand Date: </b>
                    {/* <input type="date" pattern="\d{2}-\d{2}-\d{4}" className="form-control" value={demandDate} onChange={(e)=> setDemandDate(e.target.value)}/> */}
                    <div>
                      <DatePicker
                        className="form-control"
                        selected={demandDate}
                        onChange={(date) => setDemandDate(date)}
                        dateFormat="dd/MM/yyyy"
                        readOnly={viewMood=='view'}
                      />
                    </div>
                  </>
                  }
                </div>
                <div className='col-6 mb-2'>
                  <b>Demand No:<span className='text-danger'>*</span></b>
                  <input className='form-control' type='text' onChange={(e) => setDemandNo(e.target.value)} value={DemandNo} required readOnly={viewMood=='view'}/>
                </div>
                <div className='col-6 mb-2'>
                  <b>Demand Types:<span className='text-danger'>*</span> </b>
                  <select onChange={(e) => setDemandType(e.target.value)} value={demadType} className='form-control' required={!(userInfo && userInfo.sub_organization && userInfo.sub_organization.id == 2)} disabled={window.demand_id} readOnly={viewMood=='view'}>
                    <option value="">Select</option>
                    <option value="General">General</option>
                    <option value="Urgent">Urgent</option>
                  </select>
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
                <th className='text-center'>Annual Qty. ({fy && financialYears?.find(i => i.id == fy)?.name})</th>
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
                  <td className='text-center'>{val.current_stock ? val.current_stock : 0}</td>
                  <td className='text-center'>{val?.stock?.last_3_month_unit_consume_qty ? val?.stock?.last_3_month_unit_consume_qty : 0}</td>
                  <td className='text-center'>{val?.receieved_qty ? val?.receieved_qty : 0}</td>
                  <td className='text-center'>{val.annual_qty}</td>
                  <td>
                    <input type="number" required className="form-control text-right" name="request_qty" value={val.request_qty} onChange={(e) => handleDemandPVMSValueChange(e.target, key, val)} readOnly={viewMood=='view'}/>
                  </td>
                  <td className='text-center'>0</td>
                  <td>
                    <textarea className='form-control' name='remarks' value={val.remarks} onChange={(e) => handleDemandPVMSValueChange(e.target, key, val)} readOnly={viewMood=='view'}></textarea>
                  </td>
                  <td>
                    {/* <button className='btn' type="button" onClick={() => handleDeletePVMS(key)}>
                      <i className='pe-7s-close-circle text-danger f20 font-weight-bold'></i>
                    </button> */}
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

          {/* <div className='row my-3'>

            <div className='col-md-12 gap-2'>
              <b className='mb-2'>Search PVMS :</b>
              <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangePvms} value={''} defaultOptions placeholder="PMVS No" />
            </div>

          </div> */}
          <div className='d-flex justify-content-end gap-2'>

            {(demadType != 4 && demandPVMS.length == 0) ?
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
                {viewMood!='view' && <button className='btn btn-success' disabled={isFormSubmited}>{isFormSubmited ? 'Saving...' : buttonName()}</button>}
                
              </div>
            }
          </div>
        </div>
      </div>
    </form>
  )
}

if (document.getElementById('react-querterly-demand-create')) {
  createRoot(document.getElementById('react-querterly-demand-create')).render(<CreateEditQuerterlyDemand />)
}
