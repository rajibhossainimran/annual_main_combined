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
    const [workorderReceiveData, setWorkorderReceiveData] = useState();
    const [workorderDocuments, setWorkorderDocuments] = useState('')
    const [workorderReceiveDocuments, setWorkorderReceiveDocuments] = useState('')

    useEffect(() => {
        const approval = document.querySelectorAll('.approval')

        axios.get('/get-loged-user-approval-role').then((res) => {
            window.user_approval_role = res.data
        })

        approval.forEach(element => {
            element.addEventListener('click', handleChangeApprovalClick)
        });

    }, [])

    const getWorkorderDetails = (workorder_id, workorder_receive_details) => {
        axios.get(`${window.app_url}/workorder/details-json/${workorder_id}`)
        .then((res) => {
          const data = res.data;

          setWorkorderDocuments(res.data.documents)

          let workorder_pvms_data = [];

          for (const iterator of data.workorder_pvms) {
            let delivery_data = [{
              delivery_qty: null,
              batch_no: null,
              expire_date: null,
              batch_pvms_id: null,
              pvms_store_id: null,
              workorder_receive_pvms_id: null
            }]
            if(workorder_receive_details){
              const pvms_store = workorder_receive_details.workorder_receive.pvms_store;
              if(pvms_store.length > 0 ){
                delivery_data = [];
                for (const each_pvms_store of pvms_store) {
                    if(each_pvms_store.pvms_id==iterator.pvms_id) {
                        // console.log(each_pvms_store);
                        delivery_data.push({
                            delivery_qty: each_pvms_store.batch.qty,
                            batch_no: each_pvms_store.batch.batch_no,
                            expire_date: new Date(each_pvms_store.batch.expire_date),
                            batch_pvms_id: each_pvms_store.batch.id,
                            pvms_store_id: each_pvms_store.id,
                            workorder_receive_pvms_id: each_pvms_store.workorder_receive_pvms?.id ?? null
                        })
                    }
                }
              }else{
                delivery_data = [{
                  delivery_qty: null,
                  batch_no: null,
                  expire_date: null,
                  batch_pvms_id: null,
                  pvms_store_id: null,
                  workorder_receive_pvms_id: null
                }]
              }
            }

            workorder_pvms_data.push({
              id: iterator.id,
              pvms_id: iterator.pvms?.pvms_id,
              pvms_primary_id: iterator.pvms_id,
              nomenclature: iterator.pvms?.nomenclature,
              au: iterator.pvms?.unit_name?.name,
              qty: iterator.qty,
              unit_price: iterator.unit_price,
              remarks: iterator.pvms_id,
              receiver_remarks: iterator.workorder_receive_pvms[0]?.receiver_remarks,
              delivery_mood: iterator.delivery_mood,
              total_received: iterator.workorder_receive_pvms.reduce((s, {received_qty}) => received_qty+s, 0),
              delivery_data:delivery_data
            })
          }

          setWorkorderPvms(workorder_pvms_data)
        })
      }


    const handleChangeApprovalClick = (e) => {
        const workorder_id = e.target.getAttribute('data-workorder-id');
        const workorder_receive_id = e.target.getAttribute('data-workorder-receive-id');
        const action = e.target.getAttribute('data-action');

        setViewType(action)
        if (workorder_id && workorder_receive_id) {
            setisLoading(true)

            axios.get(`${window.app_url}/workorder-receive/details-json/${workorder_receive_id}`)
                .then((res) => {
                    const data = res.data;
                    setWorkorderReceiveData(data.workorder_receive);
                    getWorkorderDetails(data.workorder_receive.workorder_id, res.data)
                    setWorkorderReceiveDocuments(res.data?.workorder_receive?.documents)
                    // setFinancialYear(data.financial_year);
                    // setContractNumber(data.contract_number)
                    // setContactDate(data.contract_date)
                    // setLastSubmitDate(data.last_submit_date)
                    // setSelectedVendor(data.vendor)
                    // setWorkorderId(data.id)

                    // let workorder_pvms_data = [];
                    // for (const iterator of data.workorder_pvms) {
                    //     workorder_pvms_data.push({
                    //         id: iterator.id,
                    //         pvms_id: iterator.pvms_id,
                    //         nomenclature: iterator.pvms.nomenclature,
                    //         au: iterator.pvms.unit_name?.name,
                    //         qty: iterator.qty,
                    //         unit_price: iterator.unit_price,
                    //         remarks: iterator.remarks,
                    //         delivery_mood: iterator.delivery_mood
                    //     })
                    // }

                    // setWorkorderPvms(workorder_pvms_data)

                    setisLoading(false)
                })
        }

    }

    // console.log(workorderPvms);
    const handleSubmitApprove = (e) => {
        e.preventDefault()

        // const data = {
        //     workorder_id: workorderId
        // }

        // Swal.fire({
        //     icon: 'warning',
        //     text: 'Do you want to approve now ?',
        //     showCancelButton: true,
        //     confirmButtonText: 'Yes, Approve Now',
        //     cancelButtonText: 'No, cancel',
        //     reverseButtons: true
        // }).then((r) => {
        //     axios.post(`${window.app_url}/workorder/approve`, data)
        //         .then((res) => {
        //             window.location.href = '/workorder'
        //         })
        // })

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
                    <div className='row'>
                        <div className='col-md-6'>
                            <b>Contract Number :</b> {workorderReceiveData?.workorder?.contract_number}
                        </div>
                        <div className='col-md-6'>
                            <b>Contract Date: </b> {workorderReceiveData?.workorder?.contract_date}
                        </div>
                        <div className='col-md-6'>
                            <b>Last Submit Date :</b> {workorderReceiveData?.workorder?.last_submit_date}
                        </div>
                        <div className='col-md-6'>
                            <b>Financial Year: </b> {workorderReceiveData?.workorder?.financial_year?.name}
                        </div>
                        {workorderDocuments && workorderDocuments.length>0 &&
                        <>
                            <div className='col-md-12 my-2'>
                                <label>Workorder Uploaded Documents:</label>
                                <div>
                                {workorderDocuments.map((item,index) => (
                                    <a className='pr-2' href={`${window.app_url}/storage/workorder_documents/${item.file}`} target='_blank'>{index+1}. <i className='fa fa-download'></i> {item.file} </a>
                                ))}
                                </div>
                            </div>

                            </>}
                        <div className='col-md-6'>
                            <b>CRV No: </b> {workorderReceiveData?.crv_no}
                        </div>
                        <div className='col-md-6'>
                            <b>Received By: </b> {workorderReceiveData?.received_by}
                        </div>
                        <div className='col-md-6'>
                            <b>Received Date: </b> {workorderReceiveData?.receiving_date}
                        </div>
                        {workorderReceiveDocuments && workorderReceiveDocuments.length>0 &&
                        <>
                            <div className='col-md-12 my-2'>
                                <label>Workorder Received Documents:</label>
                                <div>
                                {workorderReceiveDocuments.map((item,index) => (
                                    <a className='pr-2' href={`${window.app_url}/storage/workorder_receive_documents/${item.file}`} target='_blank'>{index+1}. <i className='fa fa-download'></i> {item.file} </a>
                                ))}
                                </div>
                            </div>

                            </>}
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

                                    <th>Total Receieved</th>
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
                                        <td>{val.total_received}</td>
                                        <td>{val.receiver_remarks}</td>
                                    </tr>
                                ))}

                            </tbody>
                        </table>

                        <button type="button" className="btn btn-primary" data-dismiss="modal">Close</button>
                        {/* {viewType=='approve' && <button type="submit" className="btn btn-primary ml-2" >Approve</button>} */}

                    </form>
                </div>
            }
        </div>
    )
}

if (document.getElementById('react-workorder-receive-approval')) {
    createRoot(document.getElementById('react-workorder-receive-approval')).render(<Approval />)
}
