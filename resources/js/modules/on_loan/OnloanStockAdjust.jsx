import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import axios from './../util/axios'
import AsyncSelect from 'react-select/async';
import Swal from 'sweetalert2';

export default function OnloanStockAdjust() {

  const [onLoanList, setOnLoanList] = useState([]);
  const [selectedOnLoan, setSelectedOnLoan] = useState();
  const [selectedWorkorder, setSelectedWorkorder] = useState();
  const [adjustablePvms, setAdjustablePvms] = useState([]);
  const [adjustQtyData, setAdjustQtyData] = useState([]);

  const handleOnChangeOnLoan = (value) => {
    axios.get(window.app_url+'/on-loan/api/'+value)
      .then((res)=>{
        const data = res.data;

        let pvms_data = []
        for (const element of data.on_loan_item_list) {
          const {p_v_m_s, pvms_store} = element

          pvms_data.push({
            id: element.id,
            pvms_id: p_v_m_s.id,
            receieved_qty: element.receieved_qty,
            pvms_store
          })
        }

        setSelectedOnLoan({
          id: data.id,
          is_receieved: data.is_receieved,
          reference_date: data.reference_date,
          reference_no: data.reference_no,
          vendor: data.vendor,
          pvms_data: pvms_data,
        })
      })
  }

  const loadWorkorderOptions = (inputValue, callback) => {
    axios.get(window.app_url + '/workorder/json?contract_number=' + inputValue).then((res) => {
      const data = res.data;

      let option = [];
      for (const iterator of data) {
        option.push({ value: iterator.id, label: iterator.contract_number, data: iterator })
      }

      callback(option);
    })
  };

  const handleChangeSelectWorkorder = (item, select) => {
    axios.get(`${window.app_url}/workorder/details-json/${item.data.id}`)
      .then((res) => {
        const data = res.data;

        let workorder_pvms_data = [];

        for (const iterator of data.workorder_pvms) {
            if(iterator.workorder_receive_pvms.reduce((s, {received_qty}) => received_qty+s, 0) < iterator.qty) {
                workorder_pvms_data.push({
                    id: iterator.id,
                    pvms_id: iterator.pvms?.pvms_id,
                    pvms_primary_id: iterator.pvms_id,
                    nomenclature: iterator.pvms?.nomenclature,
                    au: iterator.pvms?.unit_name?.name,
                    qty: iterator.qty,
                    unit_price: iterator.unit_price,
                    remarks: iterator.pvms_id,
                    delivery_mood: iterator.delivery_mood,
                    total_received: iterator.workorder_receive_pvms.reduce((s, {received_qty}) => received_qty+s, 0)
                  })
            }
        }

       setSelectedWorkorder({...item.data, workorder_pvms_data})
      })
  }

  const handleChangeAdjustQty = (index, value, element) => {
    // console.log(index);
    // console.log(value);
    // console.log(element);
    setAdjustQtyData((prev) => {
      let copy = [...prev];
      const index = copy.findIndex(i => i.workorder_pvms_id==element.id)

      const value_split = value.split('|')
      if(index!=-1){
        copy[index] = {...copy[index], received_qty: value_split[1], pvms_store_id: value_split[0], on_loan_item_id: element.on_loan_item_id}
      }else{
        copy.push({
          workorder_pvms_id: element.id,
          received_qty: value_split[1],
          pvms_store_id: value_split[0],
          on_loan_item_id: element.on_loan_item_id
        })
      }

      return copy
    })
  }

  useEffect(() => {
    axios.get(window.app_url+'/on-loan/list/json')
      .then((res)=>{
        setOnLoanList(res.data)
      })
  }, [])

  useEffect(() => {
    if(selectedOnLoan && selectedWorkorder){
        debugger
      // console.log(selectedWorkorder.workorder_pvms_data);
      let items = []
      for (const element of selectedWorkorder.workorder_pvms_data) {
        const on_loan_find = selectedOnLoan.pvms_data.find(d => d.pvms_id == element.pvms_primary_id)
        if(on_loan_find){

          items.push({
            id: element.id,
            workorder_id: selectedWorkorder.id,
            pvms_id: element.pvms_id,
            nomenclature: element.nomenclature,
            qty: element.qty,
            total_received: element.total_received,
            on_loan_item_id: on_loan_find.id,
            on_load_pvms_store: on_loan_find.pvms_store.map(i => ({qty: i.batch.qty, batch_no: i.batch.batch_no, pvms_store_id: i.id})),
            adjusted_pvms_store_id_list:[],
            today_received: 0
          })
        }
      }

      setAdjustablePvms(items)
    }
  }, [selectedOnLoan, selectedWorkorder])

  const handleSubmit = () => {

    axios.post(window.app_url+'/on-loan-stock-adjust/store', {adjustablePvms})
      .then((res)=>{
        window.location.href = '/on-loan'
      })
  }

  const handleSelectionOfBatchStock = (e,val,key,store_val, remaining_qty) => {

    if(val.today_received + store_val.qty > remaining_qty) {
        Swal.fire({
            icon: 'error',
            // title: 'Oops...',
            text: "You can not perform this action beacause adjust quantity can not be more than remaining quantity",
            // footer: '<a href="">Why do I have this issue?</a>'
        })

        return;
    }

    if(e.target.checked) {
        setAdjustablePvms((prev) => {
            let copy = [...prev];
            debugger
            const index = copy.findIndex(i => i.id==val.id)
            if(index!=-1){
                if(!copy[index].adjusted_pvms_store_id_list.find(i => i == store_val.pvms_store_id)) {
                copy[index].adjusted_pvms_store_id_list.push(store_val.pvms_store_id);
                copy[index] = {...copy[index],today_received:copy[index].today_received + store_val.qty}
                }
            }
            console.log(copy);
            return copy
            })
    } else {
        setAdjustablePvms((prev) => {
            let copy = [...prev];
            const index = copy.findIndex(i => i.id==val.id)
            if(index!=-1){
                if(copy[index].adjusted_pvms_store_id_list.find(i => i == store_val.pvms_store_id)) {
                copy[index] = {...copy[index],today_received:copy[index].today_received - store_val.qty,adjusted_pvms_store_id_list:copy[index].adjusted_pvms_store_id_list.filter(i => i != store_val.pvms_store_id)}
                }
            }
            console.log(copy);
            return copy
        })
    }
  }

  return (
    <div className='p-3'>
      <div className='row'>
        <div className='col-md-6'>
          <div className="form-group">
            <label className='font-weight-bold'>On Loan </label>
            <select className='form-control' onChange={(e) => handleOnChangeOnLoan(e.target.value)}>
              <option value="">Select</option>
              {onLoanList.map((val, key) => (
                <option key={key} value={val.id}>{val.reference_no}</option>
              ))}
            </select>
          </div>
          <div className="form-group">
            <label className='font-weight-bold'>Select Workorder Contract <span className="requiredStar">*</span></label>
            <AsyncSelect cacheOptions name='vendor_id' loadOptions={loadWorkorderOptions} onChange={handleChangeSelectWorkorder} placeholder="Contract Number" defaultOptions required />
          </div>
        </div>
        <div className='col-md-12'>
          <table className='table'>
            <tr>
              <th>PVMS Id</th>
              <th>Nomenclature</th>
              <th>Workorder Qty</th>
              <th>Remaining Qty</th>
              <th>Adjust Qty</th>
            </tr>
            {adjustablePvms.map((val, key) => (
              <tr key={key}>
                <td>{val.pvms_id}</td>
                <td>{val.nomenclature}</td>
                <td>{val.qty}</td>
                <td>{val.qty - val.total_received}</td>
                <td>
                  {/* <select className='form-control' onChange={(e) => handleChangeAdjustQty(key, e.target.value, val)}>
                    <option value="">Select</option>
                    {val.on_load_pvms_store.map((val, key) => (
                      <option value={val.pvms_store_id+'|'+val.qty} key={key}>Qty {val.qty} - (Batch {val.batch_no})</option>
                    ))}
                  </select> */}
                  <div>
                  <div className="form-check">
                    {val.on_load_pvms_store.map((store_val, key) => (
                    //   <option value={val.pvms_store_id+'|'+val.qty} key={key}>Qty {val.qty} - (Batch {val.batch_no})</option>
                      <div>
                        <input className="form-check-input" type="checkbox" checked={val.adjusted_pvms_store_id_list?.find(i=> i == store_val.pvms_store_id) ? 1 : 0} id={store_val.pvms_store_id} onChange={(e) => handleSelectionOfBatchStock(e,val,key,store_val,val.qty - val.total_received)}/>
                        <label className="form-check-label" htmlFor={store_val.pvms_store_id}>
                            Qty {store_val.qty} - (Batch {store_val.batch_no})
                        </label>
                      </div>
                    ))}

                    </div>
                  </div>
                  <input type='number' placeholder='Qty' className='form-control' readOnly value={val.today_received}/>
                </td>
              </tr>
            ))}
          </table>
          <button className='btn btn-primary mt-1' onClick={handleSubmit}>Submit</button>
        </div>

      </div>
    </div>
  )
}

if(document.getElementById('onloan-stock-adjust')){
  createRoot(document.getElementById('onloan-stock-adjust')).render(<OnloanStockAdjust/>)
}

