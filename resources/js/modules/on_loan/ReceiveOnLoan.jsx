import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import { PatternFormat } from 'react-number-format';
import moment from 'moment';
import DatePicker from "react-datepicker";
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import "react-datepicker/dist/react-datepicker.css";

export default function ReceiveOnLoan() {
    const [OnloanData,setOnloanData] = useState('');
    const [isLoading, setisLoading] = useState(true)
    const [isConfirmFormSubmited, setIsConfirmFormSubmited] = useState(false)
    const [UserInfo, setUserInfo] = useState('')

    useEffect(() => {
        if(window.on_loan_id) {
            axios.get(window.app_url+'/on-loan/api/'+window.on_loan_id).then((res) => {
                setisLoading(false);
                let data = {...res.data,on_loan_item_list: res.data.on_loan_item_list.map(i => {return {...i,receive_today: [{receieved_qty:'',batch_no:'',expire_date:''}]}})};
                setOnloanData(data);
            })
        }
        axios.get(window.app_url+'/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
        })
    },[])

    const handleAddAnother = (index,item) => {
        let item_received = item.receive_today.reduce((prev,curr) => curr.receieved_qty ? prev + parseInt(curr.receieved_qty) : prev , 0);
        if(((item.receieved_qty && (item.qty - item.receieved_qty) > item_received) || (!item.receieved_qty && (item.qty > item_received)))) {
            setOnloanData(prev => {
                let copy = {...prev};
                let listLength = copy.on_loan_item_list[index].receive_today.length;
                if(!copy.on_loan_item_list[index].receive_today[listLength-1].receieved_qty || !copy.on_loan_item_list[index].receive_today[listLength-1].batch_no || !copy.on_loan_item_list[index].receive_today[listLength-1].expire_date) {
                    Swal.fire({
                        icon: 'error',
                        text: "Enter Qty, Batch no and Expiry date of last receive.",
                    })
                } else {
                    copy.on_loan_item_list[index].receive_today.push({receieved_qty:'',batch_no:'',expire_date:''});
                }

                return copy;
            })
        } else {
            Swal.fire({
                icon: 'error',
                text: "Received quantity can not be greater than quantity.",
            })
        }
    }

    const handleChangeReceiveToday = (e,item,index,index_receive_today,key) => {
        if(key == 'receieved_qty') {
            let item_received = item.receive_today.filter((item,index_val) => index_val != index_receive_today).reduce((prev,curr) => curr.receieved_qty ? prev + parseInt(curr.receieved_qty) : prev , 0);
            if(((item.receieved_qty && (item.qty - item.receieved_qty - item_received) >= e.target.value) || (!item.receieved_qty && ((item.qty - item_received) >= e.target.value)))) {
                setOnloanData(prev => {
                    let copy = {...prev};
                    copy.on_loan_item_list[index].receive_today[index_receive_today] = {...copy.on_loan_item_list[index].receive_today[index_receive_today],receieved_qty:e.target.value};
                    return copy;
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    text: "Received quantity can not be negetive or greater than quantity.",
                })
            }
        } else {
            setOnloanData(prev => {
                let copy = {...prev};

                if(key == 'expire_date') {
                    copy.on_loan_item_list[index].receive_today[index_receive_today] = {...copy.on_loan_item_list[index].receive_today[index_receive_today],[key]:e};

                } else {
                    copy.on_loan_item_list[index].receive_today[index_receive_today] = {...copy.on_loan_item_list[index].receive_today[index_receive_today],[key]:e.target.value};
                }
                return copy;
            })
        }
    }

    const handleConfirmReceive = () => {
        Swal.fire({
            icon:'warning',
            text:'Do you confirm?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if(r.isConfirmed) {
                setIsConfirmFormSubmited(true);
                axios.post(window.app_url+'/on-loan-receive-stock', OnloanData).then((res) => {
                    console.log(res.data);
                    window.location.href = "/on-loan";
                })
            }
        })
    }

    return (
        <>
        <div>
           {isLoading ?
           <div className="text-center">
                <div className="ball-pulse w-100">
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                </div>
           </div>
           :
           <div>
            <div className="d-flex justify-content-between align-items-center table-header-bg py-2">
                    <h5 className="f-14">
                        Reference No
                        <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{OnloanData?.reference_no}</span>
                    </h5>

                </div>
                <div className="p-2">
                    <div className='row'>
                        <div className='col-md-12'>
                            <b>Vendor Name :</b> {OnloanData?.vendor?.name}
                        </div>
                        <div className='col-md-12'>
                            <b>Vendor Number: </b> {OnloanData?.vendor?.phone}
                        </div>
                    </div>
                    <table className='table table-bordered mt-2'>
                        <thead>
                            <tr className=''>
                                <th>Sl.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th className='text-right pr-2'>Qty</th>
                                <th>Prev. Received Qty</th>
                                <th className='text-center'>Receive</th>
                            </tr>
                        </thead>
                        <tbody>
                            {OnloanData && OnloanData.on_loan_item_list.map((item,index) => (
                                <>
                                    <tr>
                                        <td>{index+1}</td>
                                        <td>{item.p_v_m_s.pvms_id}</td>
                                        <td>{item.p_v_m_s.nomenclature}</td>
                                        <td className='text-right pr-2'>{item.qty}</td>
                                        <td>{item.item_receieve.reduce((prev,curr) => {
                                            return prev + curr.receieved_qty;
                                        } ,0)}</td>
                                        <td>
                                        {(item.qty == item.item_receieve.reduce((prev,curr) => prev + curr.receieved_qty ,0)) ?
                                        <div>Item Receieved</div>
                                        :
                                        <table className='table table-bordered mt-2'>
                                            <thead>
                                                <tr className=''>
                                                    <th className=''>Qty</th>
                                                    <th>Batch No</th>
                                                    <th>Expiry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {item.receive_today.map((i,index_receive) => (
                                                    <tr>
                                                        <td>
                                                            <input className='form-control' type='number'
                                                                value={i.receieved_qty}
                                                                onChange={(e) => handleChangeReceiveToday(e,item,index,index_receive,'receieved_qty')}
                                                            />
                                                        </td>
                                                        <td>
                                                            <input className='form-control' type='text'
                                                                value={i.batch_no}
                                                                onChange={(e) => handleChangeReceiveToday(e,item,index,index_receive,'batch_no')}
                                                            />
                                                        </td>
                                                        <td>
                                                            <DatePicker
                                                                className="form-control"
                                                                name="expire_date"
                                                                selected={i.expire_date}
                                                                // onChange={(date) => handleChangeReceivedLpitem(date,item,index,'date')}
                                                                onChange={(date) => handleChangeReceiveToday(date,item,index,index_receive,'expire_date')}
                                                                dateFormat="dd/MM/yyyy"
                                                            />
                                                        </td>
                                                    </tr>
                                                ))}
                                                 <tr>
                                                    <td colSpan={2} className='text-right'>Total</td>
                                                    <td>{item.receive_today.reduce((prev,curr) => curr.receieved_qty ? prev + parseInt(curr.receieved_qty) : prev , 0)}</td>
                                                </tr>
                                                <tr>
                                                    <td colSpan={3}>
                                                        <div className='text-right p-2'>
                                                            <button className="btn btn-success"
                                                            onClick={() => handleAddAnother(index,item)}>
                                                                Add Another
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>}

                                        </td>
                                    </tr>
                                </>
                            ))}
                        </tbody>
                    </table>
                    {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                    <div className='text-right p-2'>
                        <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmReceive()}>
                            <>{isConfirmFormSubmited ? `Confirm...`:`Confirm`}</>
                        </button>
                    </div>}
                </div>
            </div>}
        </div>
        </>
    )
}

if (document.getElementById('react-receive-on-loan')) {
    createRoot(document.getElementById('react-receive-on-loan')).render(<ReceiveOnLoan />)
}
