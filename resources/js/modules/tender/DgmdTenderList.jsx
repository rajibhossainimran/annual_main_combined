import moment from 'moment';
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import ModalComponent from '../../componants/ModalComponent';
import Paginate from '../../componants/Paginate';
import axios from './../util/axios';


export default function DgmdTenderList() {
    const [Page, setPage] = useState(0)
    const [PerPage, setPerPage] = useState(10)
    const [TenderLinks,setTenderLinks] = useState()
    const [IsLoading, setIsLoading] = useState(true)
    const [IsLoadingTenderDelete, setIsLoadingTenderDelete] = useState(false)
    const [IsShowModal, setIsShowModal] = useState(false)
    const [IsShowParticipantModal, setIsShowParticipantModal] = useState(false)
    const [IsShowParticipantVerifyDocumentModal, setIsShowParticipantVerifyDocumentModal] = useState(false)
    const [TenderItem, setTenderItem] = useState()
    const [TenderParticipants, setTenderParticipants] = useState()
    const [TenderParticipantsDocVerify, setTenderParticipantsDocVerify] = useState()
    const [TenderNotesheetDemandPVMS, setTenderNotesheetDemandPVMS] = useState()
    const [TenderList, setTenderList] = useState()
    const [isFormSubmited, setIsFormSubmited] = useState(false)
    const [UserApproval,setUserApproval] = useState();
    const [IsDocumentValidating,setIsDocumentValidating] = useState(false);
    const [ValidatingVendorId,setValidatingVendorId] = useState('');
    const [uuid, setUuid] = useState('');

    useEffect(() => {
        axios.get(window.app_url+'/get_all_tender').then((res) => {

            let tenderList = [];
            let uniq_vendor = [];
            for(const tender of res.data.data) {
                uniq_vendor = [];
                for (const csr of tender.tender_csr) {
                    for (const data of csr.vandor_perticipate) {
                        let findIndex = uniq_vendor.findIndex(i => i.id == data.vendor_id);
                        if(findIndex < 0) {
                            uniq_vendor.push(data.vendor);
                        }
                    }
                }
                tenderList.push({...tender,participants:uniq_vendor})
            }

            setTenderList(tenderList);
            setTenderLinks(res.data.links);
            setIsLoading(false)
        })
        axios.get(window.app_url+'/getLoogedUserApproval').then((res) => {
            if(res.data.user_approval_role) {
                setUserApproval(res.data.user_approval_role);
            }
        })
    },[]);

    useEffect(() => {
        if(Page>0 || PerPage != 10) {
            axios.get(window.app_url+`/get_all_tender?page=${Page}&limit=${PerPage}`).then((res) => {
                let tenderList = [];
                let uniq_vendor = [];
                for(const tender of res.data.data) {
                    uniq_vendor = [];
                    for (const csr of tender.tender_csr) {
                        for (const data of csr.vandor_perticipate) {
                            let findIndex = uniq_vendor.findIndex(i => i.id == data.vendor_id);
                            if(findIndex < 0) {
                                uniq_vendor.push(data.vendor);
                            }
                        }
                    }
                    tenderList.push({...tender,participants:uniq_vendor})
                }

                setTenderList(tenderList);
                setTenderLinks(res.data.links);
            })
        }
    },[Page,PerPage]);

    useEffect(()=>{
        if(!IsShowParticipantModal) {
            setTenderParticipants('')
        }
    },[IsShowParticipantModal])
    useEffect(()=>{
        if(!IsShowParticipantVerifyDocumentModal) {
            setTenderParticipantsDocVerify('')
        }
    },[IsShowParticipantVerifyDocumentModal])
    useEffect(()=>{
        if(!IsShowModal) {
            setTenderItem('')
        }
    },[IsShowModal])

    const handleSlelectTender = (item) => {

        // let demandPVMS = []
        setTenderItem(item)
        // for (let index = 0; index < item.tender_notesheet.length; index++) {
        //     const element = item.tender_notesheet[index];
        //     element.notesheet.notesheet_demand_p_v_m_s.forEach(demand_pvms => {

        //         let findIndex = demandPVMS.findIndex(i => i.pvms_id == demand_pvms.pvms_id)
        //         if(findIndex>-1) {
        //             demandPVMS[findIndex].demands.push(demand_pvms);
        //             demandPVMS[findIndex].qty += parseInt(demand_pvms.demand_p_v_m_s.qty);
        //         } else {
        //             demandPVMS.push ({
        //                 nomenclature: demand_pvms.demand_p_v_m_s.p_v_m_s.nomenclature,
        //                 au: demand_pvms.demand_p_v_m_s.p_v_m_s.unit_name.name,
        //                 qty: parseInt(demand_pvms.demand_p_v_m_s.qty),
        //                 demands: [demand_pvms.demand_p_v_m_s],
        //                 pvms_id: demand_pvms.pvms_id,
        //                 pvms_no: demand_pvms.demand_p_v_m_s.p_v_m_s.pvms_id,
        //                 id: demand_pvms.id
        //             })

        //         }
        //     });

        // }

        // setTenderNotesheetDemandPVMS(demandPVMS);
        setIsShowModal(true);
    }

    const handleViewParticipants = (item) => {
        setTenderParticipants(item)
        setIsShowParticipantModal(true)
    }

    const handleVerifyParticipantsDocument = (item) => {
        setTenderParticipantsDocVerify(item)
        setIsShowParticipantVerifyDocumentModal(true)
    }

    const handleValidateDocument = (vendor) => {
        let vendor_uploaded_file = TenderParticipantsDocVerify.vendor_submitted_files.filter(f => f.created_by == vendor);
        let tender_id = TenderParticipantsDocVerify.id;

        let invalidDoc = vendor_uploaded_file.filter(item => !item.is_valid);



        let data = {
            tender_id,
            vendor,
            vendor_uploaded_file,
            'valid_application' : invalidDoc.length == 0 ? 1 : 0
        }

        Swal.fire({
            icon:'warning',
            text:'Do you want to validate document?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if(r.isConfirmed){
                setIsDocumentValidating(true)
                setValidatingVendorId(vendor)
                axios.post(app_url+'/tender-verify-document', data).then((res) => {
                    console.log(res.data);
                    debugger
                    window.location.reload();
                    setIsDocumentValidating(false)
                })
            }
        })
        console.log(data);
    }

    const handleDeleteTender = (tender_id) => {
        Swal.fire({
            icon:'warning',
            text:'This operation will delete all the supporting data along with this tender. Do you want to delete this tender?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if(r.isConfirmed){
                setIsLoadingTenderDelete(true);
                axios.delete(`${app_url}/tender/${tender_id}`).then((res) => {
                    setIsLoadingTenderDelete(false);
                    window.location.reload();
                })
            }
        })
    }

    const handleFileCheckedValid = (e,file_id) => {
        debugger
        setTenderParticipantsDocVerify(prev => {
            let copy = {...prev};
            let findIndex = copy.vendor_submitted_files.findIndex(item => item.id == file_id);
            copy.vendor_submitted_files[findIndex].is_valid = e.target.checked;
            return copy;
        })

    }

    const handleDownload = async () => {
    try {
        // Remove all whitespaces and ensure it's not empty
        const cleanUuid = uuid.trim().replace(/\s+/g, '');

        if (!cleanUuid) {
            throw new Error("Please enter a valid Demand No");
        }

        // Check if the demand exists by trying to fetch it
        const response = await fetch(`/demand/download/pdfuuid/${cleanUuid}`, {
            method: 'GET',
        });

        if (!response.ok) {
            // Try to get the JSON error message from backend
            const data = await response.json();
            throw new Error(data?.error || "Failed to download PDF");
        }

        // If everything is okay, open the PDF in a new tab
        window.open(`/demand/download/pdfuuid/${cleanUuid}`, '_blank');

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Something went wrong!',
        });
    }
};

return (
    <>
        <ModalComponent
              show={IsShowModal}
              size={"xl"}
              handleClose={() => setIsShowModal(false)}
              handleShow={() => setIsShowModal(true)}
              modalTitle={
                <div className='bg-success p-2 text-white f14'>
                    CSR PVMS of Tender Number
                    <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{TenderItem && TenderItem.tender_no}</span>
                </div>}
            >
                <table className='table table-bordered'>
                    <thead>
                        <tr className=''>
                            <th>Sl.</th>
                            <th>PVMS No.</th>
                            <th>Nomenclature</th>
                            <th>A/U</th>
                            <th>Status</th>
                            <th>Last Approval</th>
                            <th className='text-right pr-2'>Quantity</th>
                            <th className='text-right pr-2'>Approved Vendor</th>
                            <th className='text-right pr-2'>Bidding Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {TenderItem && TenderItem.tender_csr && TenderItem.tender_csr.map((item,index) => (
                            <tr >
                                <td>{index+1}</td>
                                <td>{item.p_v_m_s.pvms_id}</td>
                                <td>{item.p_v_m_s.nomenclature}</td>
                                <td>{item.p_v_m_s.unit_name.name}</td>
                                <td>{item.status}</td>
                                <td className="text-uppercase">{ item.last_approval != 'head_clark' && item.last_approval_rank ? item.last_approval_rank : 'N/A'}</td>
                                <td className='text-right pr-2'>{item.pvms_quantity}</td>
                                <td className="text-uppercase">{item.approved_vendor ? item.vandor_perticipate.find(i => i.id == item.approved_vendor) ? item.vandor_perticipate.find(i => i.id == item.approved_vendor)?.vendor?.company_name : 'N/A'  : 'N/A'}</td>
                                <td className="text-uppercase">{item.approved_vendor ? item.vandor_perticipate.find(i => i.id == item.approved_vendor) ? item.vandor_perticipate.find(i => i.id == item.approved_vendor).offered_unit_price : 'N/A'  : 'N/A'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

            </ModalComponent>
            <ModalComponent
              show={IsShowParticipantModal}
              size={"xl"}
              handleClose={() => setIsShowParticipantModal(false)}
              handleShow={() => setIsShowParticipantModal(true)}
              modalTitle={
                <div className='bg-success p-2 text-white f14'>
                    Tender Participation
                    <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{TenderParticipants && TenderParticipants.tender_no}</span>
                </div>}
            >

                {/* <div className='mx-auto'>
                    {UserUploadedDocs && UserUploadedDocs.docs && UserUploadedDocs.docs.map((item,index) => (
                        <div className='mx-auto text-center pb-1'>{item.required_document ? item.required_document.name : 'Technical Submission File'} : <a href={`${window.asset_url}/vendor/upload/${item.file}`} target="_blank">{item.file}</a></div>

                    ))

                    }
                </div>  */}
                {TenderParticipants && TenderParticipants.participants &&
                TenderParticipants.participants.length == 0 ?
                <div className="text-center">No One paticipated yet!</div> :

                    <table className="table table-bordered">
                            <thead>
                                <tr className='text-left'>
                                    <th>Sl.</th>
                                    {/* <th className="">
                                        Vendor No
                                    </th> */}
                                    <th className="">
                                        Vendor Id
                                    </th>
                                    <th className="">
                                        Company Name
                                    </th>
                                    <th className="">
                                        Proprietor
                                    </th>
                                    <th className="">Phone</th>
                                    <th className="">Payment Trxid</th>
                                    <th className="">Payment On</th>
                                    <th className="text-center">Uploaded Files</th>
                                </tr>
                            </thead>

                            <tbody>
                                {TenderParticipants && TenderParticipants.participants.map((item,index) => (
                                    <tr className='text-left'>
                                        <td>{index+1}</td>
                                        {/* <td>{item.id}</td> */}
                                        <td>{item.email}</td>
                                        <td>{item.company_name}</td>
                                        <td>{item.name}</td>
                                        <td>{item.phone}</td>
                                        <td>{TenderParticipants.tender_payments.find(pay => pay.vendor_id == item.id) ?
                                         TenderParticipants.tender_payments.find(pay => pay.vendor_id == item.id).transID:'-'}</td>
                                        <td>{TenderParticipants.tender_payments.find(pay => pay.vendor_id == item.id) ?
                                         moment(TenderParticipants.tender_payments.find(pay => pay.vendor_id == item.id).created_at).format('ll'):'-'}</td>
                                        <td>
                                            {
                                                TenderParticipants.vendor_submitted_files && TenderParticipants.vendor_submitted_files.filter(f => f.created_by == item.id).map(file => (
                                                    <div className='text-center pb-1'><a href={`${window.asset_url}/vendor/upload/${file.file}`} target="_blank">{file.required_document ? file.required_document.name : 'Technical Submission File'}</a></div>
                                                ))
                                            }
                                        </td>
                                    </tr>
                                ))}

                            </tbody>

                            </table>

                }

        </ModalComponent>
            <ModalComponent
              show={IsShowParticipantVerifyDocumentModal}
              size={"xl"}
              handleClose={() => setIsShowParticipantVerifyDocumentModal(false)}
              handleShow={() => setIsShowParticipantVerifyDocumentModal(true)}
              modalTitle={
                <div className='bg-success p-2 text-white f14'>
                    Tender Participants Document Verification
                    <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{TenderParticipantsDocVerify && TenderParticipantsDocVerify.tender_no}</span>
                </div>}
            >

                {/* <div className='mx-auto'>
                    {UserUploadedDocs && UserUploadedDocs.docs && UserUploadedDocs.docs.map((item,index) => (
                        <div className='mx-auto text-center pb-1'>{item.required_document ? item.required_document.name : 'Technical Submission File'} : <a href={`${window.asset_url}/vendor/upload/${item.file}`} target="_blank">{item.file}</a></div>

                    ))

                    }
                </div>  */}
                {TenderParticipantsDocVerify && TenderParticipantsDocVerify.participants &&
                TenderParticipantsDocVerify.participants.length == 0 ?
                <div className="text-center">No One paticipated yet!</div> :

                    <table className="table table-bordered">
                            <thead>
                                <tr className='text-left'>
                                    <th>Sl.</th>
                                    {/* <th className="">
                                        Vendor No
                                    </th> */}
                                    <th className="">
                                        Vendor Id
                                    </th>
                                    <th className="">
                                        Company Name
                                    </th>
                                    <th className="">
                                        Proprietor
                                    </th>
                                    <th className="">Phone</th>
                                    {/* <th className="">Payment Trxid</th>
                                    <th className="">Payment On</th> */}
                                    <th className="width50-percent">Uploaded Files</th>
                                </tr>
                            </thead>

                            <tbody>
                                {TenderParticipantsDocVerify && TenderParticipantsDocVerify.participants.map((item,index) => (
                                    <tr className='text-left'>
                                        <td>{index+1}</td>
                                        {/* <td>{item.id}</td> */}
                                        <td>{item.email}</td>
                                        <td>{item.company_name}</td>
                                        <td>{item.name}</td>
                                        <td>{item.phone}</td>
                                        {/* <td>{TenderParticipantsDocVerify.tender_payments.find(pay => pay.vendor_id == item.id) ?
                                         TenderParticipantsDocVerify.tender_payments.find(pay => pay.vendor_id == item.id).transID:'-'}</td>
                                        <td>{TenderParticipantsDocVerify.tender_payments.find(pay => pay.vendor_id == item.id) ?
                                         moment(TenderParticipantsDocVerify.tender_payments.find(pay => pay.vendor_id == item.id).created_at).format('ll'):'-'}</td> */}
                                        <td>
                                            <table className="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>File</th>
                                                        <th className='text-center'>Is Valid</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {console.log(TenderParticipantsDocVerify.vendor_submitted_files)}
                                                    {
                                                        TenderParticipantsDocVerify.vendor_submitted_files && TenderParticipantsDocVerify.vendor_submitted_files.filter(f => f.created_by == item.id).map((file,file_index) => (
                                                            <tr>
                                                                <td><a href={`${window.asset_url}/vendor/upload/${file.file}`} target="_blank">{file.required_document ? file.required_document.name : 'Technical Submission File'}</a></td>
                                                                <td className='text-center'>
                                                                    <input name="check" id="customTenderNo" type="checkbox" checked={file.is_valid} onChange={(e) => handleFileCheckedValid(e,file.id)} disabled={file.file_checked_by}/>
                                                                </td>
                                                            </tr>
                                                        ))
                                                    }
                                                    {TenderParticipantsDocVerify.vendor_submitted_files && TenderParticipantsDocVerify.vendor_submitted_files.filter(f => f.created_by == item.id).length>0 &&
                                                        <>
                                                            <tr>
                                                                <td colSpan={2} className='text-right p-2'>
                                                                    {TenderParticipantsDocVerify.vendor_submitted_files.filter(f => f.created_by == item.id).find(item => item.file_checked_by) ?
                                                                    <>
                                                                    File checked by {TenderParticipantsDocVerify.vendor_submitted_files.filter(f => f.created_by == item.id).find(item => item.file_checked_by)?.validate_by?.name} at {moment(TenderParticipantsDocVerify.vendor_submitted_files.filter(f => f.created_by == item.id).find(item => item.file_checked_by)?.file_checked_at).format('MMM DD,yyyy h:mm a')}
                                                                    </>
                                                                    :
                                                                    <button className='btn btn-success' onClick={() => handleValidateDocument(item.id)} disabled={IsDocumentValidating && ValidatingVendorId == item.id}>
                                                                        {(IsDocumentValidating && ValidatingVendorId == item.id) ? 'Validating...': 'Validate Document'}
                                                                    </button>}
                                                                </td>
                                                            </tr>
                                                        </>
                                                    }

                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                ))}

                            </tbody>

                            </table>

                }

        </ModalComponent>

        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
            <h5 className="f-14">Tender Summary</h5>
            <div className='d-flex'>
                <Paginate setPage={setPage} Page={Page} Links={TenderLinks}/>
               {UserApproval && UserApproval.role_key == "head_clark" &&
                <a class="nav-link" href={window.app_url+'/tender/create'}>
                    <button class="btn-icon btnc btn-custom">
                        <i class="fa fa-plus btn-icon-wrapper"></i> Create Tender
                    </button>
                </a>}
            </div>

        </div>

        <div className='d-flex justify-content-between align-items-center my-2 mx-2'>
            {/* Left side: UUID input and download button */}
            <div className='d-flex gap-2'>
                <input
                    type="text"
                    className="form-control"
                    placeholder="search demand by demand no"
                    value={uuid}
                    onChange={(e) => setUuid(e.target.value)}
                    style={{ width: '300px' }}
                />
                <button className="btn btn-success ml-2" onClick={handleDownload}>
                    Search 
                </button>
            </div>

            {/* Right side: Per Page dropdown */}
            <div className='d-flex align-items-center gap-2'>
                <label className='mb-0'>Per Page</label>
                <select className="form-control" value={PerPage} onChange={e => setPerPage(e.target.value)}>
                    <option value={10}>10</option>
                    <option value={25}>25</option>
                    <option value={50}>50</option>
                    <option value={100}>100</option>
                </select>
            </div>
        </div>

        {IsLoadingTenderDelete && <div className='d-flex justify-content-center'>
            <div className='pr-2'>Tender Delete on Progress</div>
            <div>
                <div className="ball-pulse w-100">
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                </div>
            </div>
        </div>}
        <table className="table table-bordered">
            <thead>
                <tr className=''>
                    <th>Sl.</th>
                    <th className="">
                       Tender No
                    </th>
                    <th className="">
                        Publish Date
                    </th>
                    <th className="">
                        Notesheet No
                    </th>
                    <th className="">
                        Deadline
                    </th>
                    {/* <th className="">
                        Tender Sold
                    </th> */}
                    <th className="">
                        Participants
                    </th>

                    <th className="">
                       Status
                    </th>
                    <th className="text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                {IsLoading &&
                    <tr className='text-center'>
                        <td colSpan={8} className=''>
                            <div className="ball-pulse w-100">
                                <div className='spinner-loader'></div>
                                <div className='spinner-loader'></div>
                                <div className='spinner-loader'></div>
                            </div>

                        </td>

                    </tr>
                }
                {TenderList && TenderList.map((item,index)=>(
                    <tr className=''>
                        <td>{Page == 0 ? index+1+(Page*PerPage) : index+1+(Page*PerPage-PerPage)}</td>
                        <td>{item.tender_no}</td>
                        <td>{moment(item.start_date).format('ll')}</td>
                   
                   <td>

                    {item.tender_notesheet?.length > 0 ? (
                          item.tender_notesheet.map((tn, idx) => (
                            <div key={idx}>
                              <span>
                                <a
                                  target='_blank'
                                  href={`${window.app_url}/notesheet/download/pdf/${tn.notesheet_id}`}
                                  className='text-blue-600 underline mr-2'
                                >
                                  {tn.notesheet.notesheet_id}
                                
                                </a>
                              </span>
                            </div>
                          ))
                        ) : (
                          <span>—</span>
                        )}
                    </td>

                        <td>{moment(item.deadline).format('MMM DD,yyyy h:mm a')}</td>
                        {/* <td>0</td> */}
                        <td>{item.participants ?
                            <div className='d-flex align-items-end'>
                                <div>{item.participants.length}</div>
                            {moment().isAfter(moment(item.deadline)) && UserApproval && UserApproval.id == 2 &&
                                <div className='pl-2 cursor-pointer verify-doc' onClick={() => {handleVerifyParticipantsDocument(item)}}>Verify Document</div>
                            }
                            </div>
                         : 0}</td>
                        <td>
                            {moment().isBefore(moment(item.start_date)) ?
                                <span>Upcoming</span> :
                                moment().isAfter(moment(item.deadline)) ?
                                <span>Closed</span> : <span>Active</span>
                            }

                        </td>

                        <td className='text-right d-flex justify-content-end align-items-center'>
                            {item.published == 0 && UserApproval && UserApproval.id == 2 && <a onClick={() => {handleDeleteTender(item.id)}} className='mr-2' title='Tender Delete'>
                                <i className="fa fa-trash metismenu-icon cursor-pointer f20 text-danger"> </i>
                            </a>}
                            {item.published == 0 && UserApproval && UserApproval.id == 2 && <a href={window.app_url+'/tender/'+item.id+'/edit'} className='mr-2' title='Tender Edit'>
                                <i className="fa fa-edit metismenu-icon cursor-pointer f20"> </i>
                            </a>}
                            <a target='_blank' href={window.app_url+'/tender/download/pdf/'+item.id} className='mr-2' title='Tender Notice Download'>
                                <i className="fa fa-file-pdf metismenu-icon cursor-pointer f20"> </i>
                            </a>
                            <a target='_blank' href={window.app_url+'/tender/pdf/cover-letter/'+item.id} className='mr-2' title='Tender Cover Letter'>
                                <i className="fa fa-download metismenu-icon cursor-pointer f20"> </i>
                            </a>
                            {'  '}


                                {' '}
                                <div >
                                <i className="pe-7s-note2 metismenu-icon cursor-pointer f24"
                                onClick={()=> handleSlelectTender(item)}
                                > </i>
                            </div>
                            {item && item.deadline && moment().isAfter(moment(item.deadline)) && <div className="ml-2" onClick={() => handleViewParticipants(item)}>
                                    <i className="fa fa-eye metismenu-icon cursor-pointer f20"> </i>
                                </div>}

                        </td>
                    </tr>
                ))}

            </tbody>

        </table>
        <div>
            <Paginate setPage={setPage} Page={Page} Links={TenderLinks}/>
        </div>
    </>

    )
}

if (document.getElementById('react-tender-list')) {
    createRoot(document.getElementById('react-tender-list')).render(<DgmdTenderList />)
}
