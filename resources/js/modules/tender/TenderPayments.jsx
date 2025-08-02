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

export default function TenderPayments() {
    const [dateRange, setDateRange] = useState([null, null]);
    const [startDate, endDate] = dateRange;
    const [IsLoading, setIsLoading] = useState(false)
    const [monthSelect, setMonthSelect] = useState(null);
    const [TenderPayments, setTenderPayments] = useState()
    const [PaymentReportBy, setPaymentReportBy] = useState(0)

    useEffect(()=>{
        setTenderPayments();
        setDateRange([null, null])
        setMonthSelect(null)
    },[PaymentReportBy])

    useEffect(() => {
        if(startDate && endDate) {
            let start_date = `${moment(startDate).format('DD')}-${moment(startDate).format('MM')}-${moment(startDate).format('YYYY')}`; 
            let end_date = `${moment(endDate).format('DD')}-${moment(endDate).format('MM')}-${moment(endDate).format('YYYY')}`;
            setIsLoading(true)
            axios.get(`${window.app_url}/payment/payment-report/${start_date}/${end_date}`).then((res) => {
                debugger
                if(res.data) {
                    setTenderPayments(res.data)
                }
                setIsLoading(false);
            })
        }
    },[startDate,endDate])
    useEffect(() => {
        if(monthSelect) {
            let date = `${moment(monthSelect).format('MM')}-${moment(monthSelect).format('YYYY')}`; 
            setIsLoading(true)
            axios.get(`${window.app_url}/payment/payment-report-monthly/${date}`).then((res) => {
                if(res.data) {
                    setTenderPayments(res.data)
                }
                setIsLoading(false);
            })
        }
    },[monthSelect])


return (
    <>

        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
            <h5 className="f-14">Payment Report</h5>
        </div>
        <div className="my-2 px-4">
            <div className="row justify-content-end">
                <div className="col-4">
                    <b>Payment Report By: </b><br/>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_report_by" id="flexRadioDefault1" onChange={(e) => setPaymentReportBy(0)} checked={PaymentReportBy == 0}/>
                        <label class="form-check-label" for="flexRadioDefault1">
                            By Date Range
                        </label>
                        </div>
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_report_by" id="flexRadioDefault2" onChange={(e) => setPaymentReportBy(1)} checked={PaymentReportBy == 1}/>
                        <label class="form-check-label" for="flexRadioDefault2">
                            By Month 
                        </label>
                    </div>
                </div>
            </div>
            {PaymentReportBy == 0 && <div className="row mt-2">
                <div className="col-6">
                    <DatePicker
                        className='form-control'
                        selectsRange={true}
                        startDate={startDate}
                        endDate={endDate}
                        onChange={(update) => {
                            setDateRange(update);
                        }}
                        isClearable={true}
                        disabled={IsLoading}
                        placeholderText='Enter date range to see payment report'
                    />
                </div>
            </div>}
           {PaymentReportBy == 1 && <div className="row mt-2">
                <div className="col-6">
                <DatePicker
                    className='form-control'
                    selected={monthSelect}
                    onChange={(date) => setMonthSelect(date)}
                    dateFormat="MM/yyyy"
                    isClearable={true}
                    disabled={IsLoading}
                    placeholderText='Enter month to see monthly payment report'
                    showMonthYearPicker
                    showFullMonthYearPicker
                />
                </div>
            </div>}

        </div>

       {((startDate && endDate) || (monthSelect)) && 
            <table className="table table-bordered px-2">
                <thead>
                    <tr className=''>
                        <th className='width5-percent'>Sl.</th>
                        <th className='width20-percent'>
                        Tender No
                        </th>
                        <th className='width15-percent'>
                        Payment By
                        </th>
                        <th className='width15-percent'>Tender Fee(BDT)</th>
                        <th className='width15-percent'>DGMS Fee(BDT)</th>
                        <th className='width15-percent'>SSL Fee(BDT)</th>
                        <th className="text-right pr-1 width15-percent">
                            Total Amount(BDT)
                        </th>
                    </tr>
                </thead>

                <tbody>
                    {IsLoading &&
                        <tr className='text-center'>
                            <td colSpan={7} className=''>
                                <div className="ball-pulse w-100">
                                    <div className='spinner-loader'></div>
                                    <div className='spinner-loader'></div>
                                    <div className='spinner-loader'></div>
                                </div>

                            </td>

                        </tr>
                    }
                    {TenderPayments && TenderPayments.length==0 &&
                        <tr className='text-center'>
                            <td colSpan={7} className=''>
                              <div>No data found!</div>
                            </td>
                        </tr>
                    }
                    {TenderPayments && TenderPayments.map((item,key) => (
                        <tr className='' key={key}>
                            <td className='width5-percent'>{key+1}</td>
                            <td className='width20-percent'>
                              {item.tender.tender_no}
                            </td>
                            <td className='width15-percent'>
                                {item.vendor.company_name}
                            </td>
                            <td className='width15-percent width15-percent'>
                                {item.tender_fee} TK    
                            </td>
                            <td className='width15-percent'>
                                {item.dgms_fee} TK
                            </td>
                            <td className='width15-percent'>
                                {item.ssl_fee} TK
                            </td>
                            <td className="text-right pr-1 width15-percent">
                                {item.amount} TK
                            </td>
                        </tr>
                    ))}
                    {TenderPayments && TenderPayments.length>0 && 
                        <tr>
                            <td className='width5-percent'></td>
                            <td className='width20-percent'>
                            </td>
                            <td className='width15-percent'>
                            </td>
                            <td className='width15-percent font-weight-bold'>
                                {TenderPayments.reduce((prev,current) => prev + current.tender_fee,0)} TK
                            </td>
                            <td className='width15-percent font-weight-bold'>
                                {TenderPayments.reduce((prev,current) => prev + current.dgms_fee,0)} TK
                            </td>
                            <td className='width15-percent font-weight-bold'>
                                {TenderPayments.reduce((prev,current) => prev + current.ssl_fee,0)} TK
                            </td>
                            <td className="text-right pr-1 width15-percent font-weight-bold">
                                {TenderPayments.reduce((prev,current) => prev + current.amount,0)} TK
                            </td>
                        </tr>
                    }
           

                </tbody>

            </table>
        }
    </>

    )
}

if (document.getElementById('react-tender-payment')) {
    createRoot(document.getElementById('react-tender-payment')).render(<TenderPayments />)
}
