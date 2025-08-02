import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import moment from 'moment';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";

export default function UnitWiseItemIssue() {
    const [financialYears, setFinancialYears] = useState([]);
    const [financialYear, setFinancialYear] = useState('');
    const [selectedPvms, setSelectedPvms] = useState('');
    const [IsFromSubmit, setIsFromSubmit] = useState(false);
    const [Unit,setUnit] = useState('')
    const [Data,setData] = useState('')

    useEffect(() => {
        axios.get(`${window.app_url}/settings/financial-years/api`)
        .then((res) => {
            setFinancialYears(res.data)
        })
    },[])

    useEffect(() => {
        setData('')
    },[financialYear,Unit,selectedPvms])

    const loadOptions = (inputValue, callback) => {
        axios.get(window.app_url+'/settings/pvms/search?keyword='+inputValue).then((res)=>{
            const data = res.data;

            let option=[];
            for (const iterator of data) {
            option.push({value:iterator.id, label:iterator.pvms_id+' - '+iterator.nomenclature+' - '+ (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data:iterator})
            }

            callback(option);
        })
    };

    const loadUnitOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/unit-list-api?search=' + inputValue).then((res) => {
          const data = res.data;

          let option = [];
          for (const iterator of data) {
            option.push({ value: iterator.id, label: iterator.name, data: iterator })
          }

          callback(option);
        })
    };

    const handleChangeSelectUnit = (option, select) => {
        if(select.action == 'select-option') {
            setUnit(option.value);
        } else {
            setUnit('')
        }
    }


    const handleChangePvms = (option, select) => {
        debugger
        if(select.action == 'select-option') {
            setSelectedPvms(option.value);
        } else {
            setSelectedPvms('')
        }

    }

    const handleSubmit = (e) => {
        e.preventDefault();
        setIsFromSubmit(true);
        let data = {
            unit: Unit,
            pvms_id: selectedPvms,
            fy: financialYear
        }

        axios.post(window.app_url + '/unit-wise-item-issue',data)
        .then((res) => {
            debugger
            setIsFromSubmit(false);
            setData(res.data);
        })
        .catch((err) => {
            setIsFromSubmit(false);
        })
    }

return (
    <>
        <form onSubmit={handleSubmit}>
            <div className='row p-2'>
                <div className='col-lg-6'>
                    <label>Financial Years <span className='text-danger'>*</span></label>
                    <select className='form-control' id="fy" name="fy" required value={financialYear} onChange={(e) => setFinancialYear(e.target.value)}>
                        <option value="">Select</option>
                        {financialYears.map((val, key) => (
                        <option key={key} value={val.id}>{val.name}</option>
                        ))}
                    </select>
                </div>
                <div className="col-lg-6">
                    <div className="form-group">
                    <label>PVMS  <span className='text-danger'>*</span></label>
                    <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleChangePvms} defaultOptions placeholder="PMVS No" isClearable={true} required/>
                    </div>
                </div>
                <div className='col-lg-6'>
                    <div className="form-group">
                        <label>Unit Name</label>
                        <AsyncSelect cacheOptions name='unit_name' loadOptions={loadUnitOptions} onChange={handleChangeSelectUnit} placeholder="Unit Name" defaultOptions isClearable={true}/>
                    </div>
                </div>
                <div className='col-lg-12'>
                    <button type='submit' className='btn btn-success' disabled={IsFromSubmit}>{IsFromSubmit ? 'Show...' : 'Show'}</button>
                </div>
            </div>
        </form>
        {Data && <div className='p-2'>
            <table className='table table-bordered'>
                <tbody>
                    <tr>
                        <th colSpan={4}>
                            Current Stock: {Data.stock_data && Data.stock_data[0] && Data.stock_data[0].afmsd_stock_qty ? Data.stock_data[0].afmsd_stock_qty : 0}
                        </th>
                        <th colSpan={4}>
                            Contract Quantity: {Data?.contract_item.reduce((prev,curr) => curr.qty+prev,0)}
                        </th>
                    </tr>
                    {Data?.contract_item_receive && Data?.contract_item_receive.length>0 && <>
                    <tr>
                        <th>Nomenclature</th>
                        <th>Expire Date</th>
                        <th>Quantity</th>
                        <th>Supplier Name</th>
                        <th>Contract No</th>
                        <th>CRV</th>
                        <th>Receive Date</th>
                        <th>Receive Qty</th>
                    </tr>
                    {Data?.contract_item_receive.map(item => (
                        <tr>
                        <td>{item?.workorder_pvms?.pvms?.nomenclature}</td>
                        <td>{item.on_loan_item_id ?
                                item?.on_loan_item?.pvms_store?.batch?.expire_date
                                :
                                item?.pvms_store?.batch?.expire_date
                            }</td>
                        <td>{item?.workorder_pvms?.qty}</td>
                        <td>{item?.workorder_pvms?.workorder?.vendor?.name}</td>
                        <td>{item?.workorder_pvms?.workorder?.contract_number}</td>
                        <td>{item?.work_order_receive?.crv_no}</td>
                        <td>{item?.work_order_receive?.receiving_date}</td>
                        <td>{item?.received_qty}</td>
                    </tr>
                    ))}
                    </>}
                </tbody>
            </table>
            <div className='text-center'>
                <b>Loan Issue Quantity: {Data?.on_loan_issue_qty}</b>
            </div>
            <table className='table table-bordered'>
                <tbody>
                    <tr>
                        <th>Yearly Expense</th>
                        <th>Annual Requirement</th>
                        <th>Total Issued</th>
                        <th>Due</th>
                    </tr>
                    <tr>
                        <td>{Data.stock_data && Data.stock_data[0] && Data.stock_data[0].last_12_month_afmsd_consume_qty ? Data.stock_data[0].last_12_month_afmsd_consume_qty : 0}</td>
                        <td>{Data.annual_demand_pvms ? Data.annual_demand_pvms.total_qty : "N/A"}</td>
                        <td>{Data?.item_issue?.reduce((prev,curr) => curr.request_qty + prev , 0)}</td>
                        <td>{Data?.item_issue?.reduce((prev,curr) => curr.request_qty + prev , 0) - Data?.item_issue?.reduce((prev,curr) => curr.received_qty + prev , 0)}</td>
                    </tr>
                </tbody>
            </table>
            {Data?.item_issue && Data?.item_issue?.length>0 &&
            <table className='table table-bordered'>
                <tbody>
                    <tr>
                        <th>Demand</th>
                        <th>PVMS No</th>
                        <th>Nomenclature</th>
                        <th>A/U</th>
                        <th>Qty Issue</th>
                        <th>Voucher No</th>
                        <th>Unit</th>
                    </tr>
                   {Data?.item_issue?.map(item => (
                        <tr>
                            <td>{item?.demand?.uuid}</td>
                            <td>{item?.pvms?.pvms_id}</td>
                            <td>{item?.pvms?.nomenclature}</td>
                            <td>{item?.pvms?.unit_name?.name}</td>
                            <td>{item?.request_qty}</td>
                            <td>{item?.purchase?.purchase_number}</td>
                            <td>{item?.purchase?.dmd_unit?.name}</td>
                        </tr>
                    ))}
                </tbody>
            </table>}
        </div>}
    </>
)}

if (document.getElementById('react-unit-wise-item-issue')) {
    createRoot(document.getElementById('react-unit-wise-item-issue')).render(<UnitWiseItemIssue />)
}
