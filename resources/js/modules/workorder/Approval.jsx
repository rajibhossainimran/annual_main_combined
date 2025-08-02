import axios from './../util/axios'
import moment from 'moment';
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';

export default function Approval() {

    const [workorderPvms, setWorkorderPvms] = useState([])
    const [selectedVendor, setSelectedVendor] = useState();
    const [contractNumber, setContractNumber] = useState();
    const [contactDate, setContactDate] = useState();
    const [lastSubmitDate, setLastSubmitDate] = useState();
    const [financialYear, setFinancialYear] = useState([]);
    const [isLoading, setisLoading] = useState(false);
    const [viewType, setViewType] = useState(false);
    const [workorderId, setWorkorderId] = useState();
    const [Documents, setDocuments] = useState('');

    useEffect(() => {
        const approval = document.querySelectorAll('.approval')

        axios.get('/get-loged-user-approval-role').then((res) => {
            window.user_approval_role = res.data
        })

        approval.forEach(element => {
            element.addEventListener('click', handleChangeApprovalClick)
        });

    }, [])


    const handleChangeApprovalClick = (e) => {
        const workorder_id = e.target.getAttribute('data-workorder-id');
        const action = e.target.getAttribute('data-action');

        setViewType(action)
        if (workorder_id) {
            setisLoading(true)

            axios.get(`${window.app_url}/workorder/details-json/${workorder_id}`)
                .then((res) => {
                    const data = res.data;

                    setFinancialYear(data.financial_year);
                    setContractNumber(data.contract_number)
                    setContactDate(data.contract_date)
                    setLastSubmitDate(data.last_submit_date)
                    setSelectedVendor(data.vendor)
                    setWorkorderId(data.id)
                    setDocuments(data.documents)

                    let workorder_pvms_data = [];
                    for (const iterator of data.workorder_pvms) {
                        workorder_pvms_data.push({
                            id: iterator.id,
                            pvms_id: iterator.pvms_id,
                            nomenclature: iterator.pvms.nomenclature,
                            au: iterator.pvms.unit_name?.name,
                            qty: iterator.qty,
                            unit_price: iterator.unit_price,
                            remarks: iterator.remarks,
                            delivery_mood: iterator.delivery_mood
                        })
                    }

                    setWorkorderPvms(workorder_pvms_data)

                    setisLoading(false)
                })
        }

    }

    const handleSubmitApprove = (e) => {
        e.preventDefault()

        const data = {
            workorder_id: workorderId
        }

        Swal.fire({
            icon: 'warning',
            text: 'Do you want to approve now ?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve Now',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((r) => {
            axios.post(`${window.app_url}/workorder/approve`, data)
                .then((res) => {
                    window.location.href = '/workorder'
                })
        })

    }

    return (
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
                    <div className='row mb-4'>
                        <div className='col-md-6'>
                            <b>Contract Number :</b> {contractNumber}
                        </div>
                        <div className='col-md-6'>
                            <b>Contract Date: </b> {contactDate}
                        </div>
                        <div className='col-md-6'>
                            <b>Last Submit Date :</b> {lastSubmitDate}
                        </div>
                        <div className='col-md-6'>
                            <b>Financial Year: </b> {financialYear.name}
                        </div>
                        <div className='col-md-6'>
                        {Documents && Documents.length > 0 &&
                        <>
                        <div className='my-2'>
                            <label>Uploaded Documents:</label>
                            <div>
                            {Documents.map((item,index) => (
                                <a className='pr-2' href={`${window.app_url}/storage/workorder_documents/${item.file}`} target='_blank'>{index+1}. <i className='fa fa-download'></i> {item.file} </a>
                            ))}
                            </div>
                        </div>

                        </>
                        }
                        </div>
                    </div>

                    <form onSubmit={handleSubmitApprove}>
                        <table className="table">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>PVMS</th>
                                    <th>Nomenclature</th>
                                    <th>Specification</th>
                                    <th>A/U</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Price</th>
                                    <th>Delivery Mode</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                {workorderPvms.map((val, key) => (
                                    <tr key={key}>
                                        <td>{key + 1}</td>
                                        <td>{val.pvms_id}</td>
                                        <td>{val.nomenclature}</td>
                                        <td>{val.details}</td>
                                        <td>{val.au}</td>
                                        <td>{val.qty}</td>
                                        <td>{val.unit_price}</td>
                                        <td>{(val.qty * val.unit_price).toFixed(2)}</td>
                                        <td>{val.delivery_mood}</td>
                                        <td>{val.remarks}</td>
                                    </tr>
                                ))}

                            </tbody>
                        </table>

                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                        {viewType=='approve' && <button type="submit" className="btn btn-primary ml-2" >Approve</button>}

                    </form>
                </div>
            }
        </div>
    )
}

if (document.getElementById('react-workorder-approval')) {
    createRoot(document.getElementById('react-workorder-approval')).render(<Approval />)
}
