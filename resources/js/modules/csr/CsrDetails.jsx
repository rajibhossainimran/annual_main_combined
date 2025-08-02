import axios from './../util/axios'
import React, { useEffect, useState } from 'react'
import { createRoot } from 'react-dom/client'
import AsyncSelect from 'react-select/async';
import InputSearch from '../../componants/InputSearch';
import ModalComponent from '../../componants/ModalComponent';
import Swal from 'sweetalert2'
import { PatternFormat } from 'react-number-format';
import moment from 'moment';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import Paginate from '../../componants/Paginate';
import RemarksTemplate from '../../componants/RemarksTemplate';

export default function CsrDetails() {

    const [TenderId,setTenderId] = useState()
    const [VendorPerticipationList,setVendorPerticipationList] = useState([])
    const [TenderDetails,setTenderDetails] = useState()
    const [IsLoadingCsr,setIsLoadingCsr] = useState(false)
    const [IsLoadingUserUploadedDocs,setIsLoadingUserUploadedDocs] = useState(false)
    const [UserUploadedDocs,setUserUploadedDocs] = useState()
    const [IsShowModal,setIsShowModal] = useState(false)
    const [IsShowPvmsModal,setIsShowPvmsModal] = useState(false)
    const [SelectedCsrPvms,setSelectedCsrPvms] = useState()
    const [CsrPvmsApprovalSteps,setCsrPvmsApprovalSteps] = useState()
    const [UserApproval,setUserApproval] = useState();
    const [IsGivingTenderApproval,setIsGivingTenderApproval] = useState(false);
    const [isFormSubmited, setIsFormSubmited] = useState(false)
    const [Remarks, setRemarks] = useState('')
    const [SelectedBidder, setSelectedBidder] = useState('')
    const [currentApprovalSteps, setCurrentApprovalSteps] = useState([])
    const [CsrList, setCsrList] = useState([])
    const [CsrNavLink, setCsrNavLink] = useState([])
    const [Page, setPage] = useState(0)
    const [PerPage, setPerPage] = useState(10)
    const [approvalSteps, setApprovalSteps] = useState()
    const [SelectedHod, setSelectedHod] = useState()
    const [HodRequired, setHodRequired] = useState(false)
    const [BidderChange, setBidderChange] = useState(false)
    const [LoggedUser, setLoggedUser] = useState()
    const [userApprovalRole, setUserApprovalRole] = useState([])

    useEffect(() => {
        setIsLoadingCsr(true)
        axios.get(`${window.app_url}/get-tender-with-csr-pvms`).then((res) => {

            if(res.data) {
                if(res.data.csr_list && res.data.csr_list.data) {

                    // let uniq_vendor = [];
                    // for (const csr of res.data.csr_list.data) {
                    //     for (const data of csr.vandor_perticipate_with_valid_doc) {
                    //         let findIndex = uniq_vendor.findIndex(i => i.id == data.vendor_id);
                    //         if(findIndex < 0) {
                    //             uniq_vendor.push(data.vendor);
                    //         }
                    //     }
                    // }
                    let pending_csr = res.data.pending_csr_for_user;
                    let csr_data = res.data.csr_list.data.filter(item => !res.data.pending_csr_ids.includes(item.id));
                    let pending_along_prev_csr = [...pending_csr,...csr_data];


                    setCsrList(pending_along_prev_csr);
                    setCsrNavLink(res.data.csr_list.links);
                    // setVendorPerticipationList(uniq_vendor)
                }

                if(res.data.csr_next_steps) {
                    let pending_csr_next_steps = res.data.pending_csr_nextsteps;
                    let csr_next_steps = res.data.csr_next_steps.filter(item => !res.data.pending_csr_ids.includes(item.id));
                    let pending_along_prev_csr_next_steps = [...pending_csr_next_steps,...csr_next_steps];
                    setCsrPvmsApprovalSteps(pending_along_prev_csr_next_steps)
                }

            }
            setIsLoadingCsr(false)
        })
        axios.get(window.app_url+'/getLoogedUserApproval').then((res) => {
            if(res.data) {
                setLoggedUser(res.data)
            }
            if(res.data.user_approval_role) {
                setUserApproval(res.data.user_approval_role);
            }
        })
        axios.get(window.app_url+'/csr-approval-steps').then((res) => {
            setApprovalSteps(res.data)
        })
        axios.get(window.app_url+'/user-approval-roles').then((res)=>{
            setUserApprovalRole(res.data)
        })
    },[])

    useEffect(() => {
        axios.get(`${window.app_url}/get-tender-with-csr-pvms?page=${Page}&limit=${PerPage}`).then((res) => {
            if(res.data) {
                if(res.data) {
                    if(res.data.csr_list && res.data.csr_list.data) {

                        // let uniq_vendor = [];
                        // for (const csr of res.data.csr_list.data) {
                        //     for (const data of csr.vandor_perticipate_with_valid_doc) {
                        //         let findIndex = uniq_vendor.findIndex(i => i.id == data.vendor_id);
                        //         if(findIndex < 0) {
                        //             uniq_vendor.push(data.vendor);
                        //         }
                        //     }
                        // }
                        let pending_csr = res.data.pending_csr_for_user;
                        let csr_data = res.data.csr_list.data.filter(item => !res.data.pending_csr_ids.includes(item.id));
                        let pending_along_prev_csr = [...pending_csr,...csr_data];


                        setCsrList(pending_along_prev_csr);
                        setCsrNavLink(res.data.csr_list.links);
                        // setVendorPerticipationList(uniq_vendor)
                    }

                    if(res.data.csr_next_steps) {
                        let pending_csr_next_steps = res.data.pending_csr_nextsteps;
                        let csr_next_steps = res.data.csr_next_steps.filter(item => !res.data.pending_csr_ids.includes(item.id));
                        let pending_along_prev_csr_next_steps = [...pending_csr_next_steps,...csr_next_steps];
                        setCsrPvmsApprovalSteps(pending_along_prev_csr_next_steps)
                    }

                }

            }
        })
    },[Page,PerPage]);

    useEffect(() => {
        // axios.get(window.app_url+'/getLoogedUserApproval').then((res) => {
        //     if(res.data.user_approval_role) {
        //         setUserApproval(res.data.user_approval_role);
        //     }
        // })
        if(UserApproval && UserApproval.hod_selection) {
            setHodRequired(true);
        }


    },[UserApproval])


    useEffect(() => {
        if(!IsShowModal) {
            setUserUploadedDocs('')
        }
    },[IsShowModal]);

    useEffect(() => {
        if(!IsGivingTenderApproval && !IsShowPvmsModal) {
            setIsGivingTenderApproval(false)
            setRemarks('')
            setSelectedBidder('')
            setSelectedHod('')
            setBidderChange(false)
            setTenderDetails('')
        }
    },[IsShowPvmsModal]);

    const loadOptions = (inputValue, callback) => {
        axios.get(window.app_url+'/get-tender-for-csr?keyword='+inputValue).then((res)=>{
          const data = res.data;

          let option=[];
          for (const iterator of data) {
            option.push({value:iterator.id, label:iterator.tender_no, data:iterator})
          }

          callback(option);
        })
      };

      const handleSelectTender = (value) => {
        setTenderId(value.data.id);
      }

    const handleViewParticipantUploadedDocuments = (user_id) => {
        setIsLoadingUserUploadedDocs(true)
        axios.get(`${window.app_url}/get_user_submitted_docs/${TenderId}/${user_id}`).then((res) => {
            debugger
            if(res.data) {
                setUserUploadedDocs({
                    user_id:user_id,
                    docs:res.data
                })
            }
            setIsLoadingUserUploadedDocs(false)
        })
        setIsShowModal(true)

    }

    const handleApproveCsrPvms = item => {
        setIsGivingTenderApproval(true);
        handleViewCsrPvms(item);
    }

    const csr_ApprovalTypeSwitch = (item_type,is_dental) => {
        if((item_type == 1 || item_type == 3) && (is_dental == 1)) {
            setCurrentApprovalSteps(approvalSteps['Dental'])
        } else if(item_type == 1) {
            setCurrentApprovalSteps(approvalSteps['EMProcurementSteps'])
        } else if(item_type == 3) {
            setCurrentApprovalSteps(approvalSteps['Medicine'])
        } else if(item_type == 4) {
            setCurrentApprovalSteps(approvalSteps['Reagent'])
        } else if(item_type == 5) {
            setCurrentApprovalSteps(approvalSteps['Disposable'])
        }

        return;
    }

    const handleViewCsrPvms = (item) => {
        debugger
        if(item.approved_vendor) {
            setSelectedBidder(item.approved_vendor)
        }
        setTenderDetails(item.tender)
        csr_ApprovalTypeSwitch(item.p_v_m_s.item_typename.id,item.csr_demands[0].notesheet.is_dental);
        setSelectedCsrPvms(item)
        setIsShowPvmsModal(true)
    }

    const handleSubmitApprove = (item) => {
       if(!Remarks && SelectedCsrPvms && SelectedCsrPvms.vandor_perticipate_with_valid_doc && SelectedCsrPvms.vandor_perticipate_with_valid_doc[0] && SelectedCsrPvms.vandor_perticipate_with_valid_doc[0].offered_unit_price && SelectedBidder && SelectedCsrPvms.vandor_perticipate_with_valid_doc.find(item => item.id == SelectedBidder) && parseFloat(SelectedCsrPvms.vandor_perticipate_with_valid_doc.find(item => item.id == SelectedBidder).offered_unit_price )> parseFloat(SelectedCsrPvms.vandor_perticipate_with_valid_doc[0].offered_unit_price)) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `Please Provide Reamrks for not choosing the lowest unit price offer by the bidder!`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            return;
       }

    //    if(UserApproval && UserApproval.role_key == 'hod' && !SelectedBidder) {
    //         Swal.fire({
    //             icon: 'error',
    //             // title: 'Oops...',
    //             text: `Please select a bidder!`,
    //             // footer: '<a href="">Why do I have this issue?</a>'
    //         })
    //         return;
    //    }

       if(HodRequired && !SelectedHod) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `Please Select HOD for next approval!`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
            return;
       }
        const data = {
            csr_id: item.id,
            remarks: Remarks,
            selected_biddder_id: BidderChange ? SelectedBidder : '',
            hod_user: SelectedHod
        }

        Swal.fire({
            icon:'warning',
            text:'Do you want to approve now ?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve Now',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                setIsFormSubmited(true);
                axios.post(window.app_url+'/csr-approval', data).then((res) => {
                    console.log(res.data);
                    window.location.reload();
                })
            }
          })

    }

    const showTenderDetailsInformation = () => {
        return (
            <>
            {TenderDetails &&
                <div>
                    <div><b>Tender No:</b> {TenderDetails.tender_no}</div>
                    <div><b>Start Date:</b> {moment(TenderDetails.start_date).format('ll')}</div>
                    <div><b>End Date: </b>{moment(TenderDetails.deadline).format('LLLL')}</div>
                    <div><b>Purchase Price:</b> {TenderDetails.purchase_price}</div>
                    <div><b>Technical Submission File: </b>
                    {window.asset_url ?
                    <>
                    <a href={`${window.asset_url}/tender-submission/${TenderDetails.submission_file_name}`} target="_blank">{TenderDetails.submission_file_name}</a>
                    </>
                    :
                    TenderDetails.submission_file_name}
                     </div>
                    <div><b>Technical Terms & Conditions File: </b>
                    {window.asset_url ?
                    <>
                    <a href={`${window.asset_url}/tender-submission/${TenderDetails.terms_conditions_file}`} target="_blank">{TenderDetails.terms_conditions_file}</a>
                    </>
                    :
                    TenderDetails.terms_conditions_file}
                     </div>
                    <div><b>Technical Submission File: </b>
                    {window.asset_url ?
                    <>
                    <a href={`${window.asset_url}/tender-submission/${TenderDetails.requirements_file}`} target="_blank">{TenderDetails.requirements_file}</a>
                    </>
                    :
                    TenderDetails.requirements_file}
                     </div>
                </div>

            }
            </>
        )
    }

    const loadOptionsforHod = (inputValue, callback) => {
        axios.get(window.app_url+'/get-hod-users?keyword='+inputValue).then((res)=>{
          const data = res.data;

          let option=[];
          for (const iterator of data) {
            option.push({value:iterator.id, label:iterator.name+' - '+iterator.email, data:iterator})
          }

          callback(option);
        })
      };

    const handleSelectHod = (item) => {
        debugger
        setSelectedHod(item.data.id);
    }

    return (
        <>
        <ModalComponent
              show={IsShowPvmsModal}
              size={"xl"}
              handleClose={() => setIsShowPvmsModal(false)}
              handleShow={() => setIsShowPvmsModal(true)}
              modalTitle={
                <div className='bg-success p-2 text-white f14'>
                    Csr PVMS
                    <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{SelectedCsrPvms && SelectedCsrPvms.p_v_m_s && SelectedCsrPvms.p_v_m_s.pvms_id}</span>
                </div>}
            >
            <div className='px-4'>
                <div className='row'>
                    <div className='col-4'>
                        <div><b>Nomenclature:</b> {SelectedCsrPvms && SelectedCsrPvms.p_v_m_s && SelectedCsrPvms.p_v_m_s.nomenclature}</div>
                        <div><b>A/U:</b> {SelectedCsrPvms && SelectedCsrPvms.p_v_m_s && SelectedCsrPvms.p_v_m_s.unit_name.name}</div>
                        <div><b>Item Type:</b> {SelectedCsrPvms && SelectedCsrPvms.p_v_m_s && SelectedCsrPvms.p_v_m_s.item_typename && SelectedCsrPvms.p_v_m_s.item_typename.name}</div>
                        <div><b>Qunatity:</b> {SelectedCsrPvms && SelectedCsrPvms.p_v_m_s && SelectedCsrPvms.pvms_quantity}</div>
                    </div>
                    <div className="col-8 d-flex justify-content-end">
                        {showTenderDetailsInformation()}
                    </div>
                </div>

                <table className='table table-bordered mt-4'>
                    <thead>
                        <tr className=''>
                            <th></th>
                            <th>Sl.</th>
                            <th>Bidder</th>
                            <th>Specification</th>
                            <th className='text-right pr-2'>Unit Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        {SelectedCsrPvms && SelectedCsrPvms.vandor_perticipate_with_valid_doc && SelectedCsrPvms.vandor_perticipate_with_valid_doc.map((item,index) => (
                            <tr >
                                <td>
                                    <div className="position-relative custom-control custom-checkbox">
                                        <input className="form-check-input input-check-accent" disabled={!IsGivingTenderApproval || UserApproval.role_key == "head_clark"} name={`selected_bidder_for_${SelectedCsrPvms.id}`} type="radio" value={item.id} onChange={(e) => {
                                            setSelectedBidder(e.target.value)
                                            setBidderChange(true);
                                        }} checked={SelectedBidder && item.id == SelectedBidder}  aria-label="..."/>
                                    </div>
                                </td>
                                <td>{index+1}</td>
                                <td>{item.vendor.company_name}</td>
                                <td>
                                    <div
                                        dangerouslySetInnerHTML={{__html: item.details}}
                                    />
                                </td>
                                <td className='text-right pr-2'>{item.offered_unit_price}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                <div className='row mb-2'>
                    <div className='col-md-6'>
                    {SelectedCsrPvms && SelectedCsrPvms.csr_pvms_approval && SelectedCsrPvms.csr_pvms_approval.length>1 && <div className='padding-10 antiquewhite-bg'>
                            <h5>Approvals</h5>
                            <table className='table'>
                                <thead>
                                    <tr>
                                        <th className='width50-percent'>Approve By</th>
                                        <th>Remark</th>
                                        <th>Selected Bidder</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {SelectedCsrPvms.csr_pvms_approval.map((val) => (
                                        <>
                                        {(val.role_name!='head_clark') &&
                                        <tr>
                                            <td>
                                                <input type='checkbox' checked/> {' '}

                                                {
                                                    val.role_name == 'hod' && SelectedCsrPvms.hod ? <>{` ${SelectedCsrPvms.hod.email} `}<span className="f12">({SelectedCsrPvms.hod.name})</span></>
                                                :
                                                <>{userApprovalRole?.find(i => i.role_key==val.role_name)?.role_name}</>
                                                }
                                                {/* {currentApprovalSteps.find(i => i.designation==val.role_name)?.name }                                                 */}
                                            </td>
                                            <td>
                                                {val.remarks}
                                            </td>
                                            <td>
                                                {val.bidder && val.bidder.vendor && val.bidder.vendor.company_name}
                                            </td>
                                        </tr>
                                        }
                                        </>
                                    ))}
                                </tbody>
                            </table>
                            {/* {currentApprovalSteps.map((val, key) => (
                                <div>
                                    {key > 1 && <><input type='checkbox' checked={demandApproval.find(i => i.role_name==val.designation)}/> {val.name}</> }

                                </div>

                            ))} */}
                        </div>
                    }
                    </div>
                    <div className='col-md-6'>
                        {SelectedCsrPvms && SelectedCsrPvms.csr_pvms_approval && SelectedCsrPvms.csr_pvms_approval[0] && SelectedCsrPvms.csr_pvms_approval[0].remarks && <b>Head Clark Note</b>}
                        {SelectedCsrPvms && SelectedCsrPvms.csr_pvms_approval && SelectedCsrPvms.csr_pvms_approval[0] && SelectedCsrPvms.csr_pvms_approval[0].remarks && (
                            <p>
                                1. {SelectedCsrPvms.csr_pvms_approval[0].remarks}
                            </p>
                        )}
                        <div>
                            {SelectedCsrPvms && CsrPvmsApprovalSteps && CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id) && CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id).step && CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id).step.designation == 'hod' && SelectedCsrPvms.hod &&
                            <>
                            Forwarded to HOD - {SelectedCsrPvms.hod.name} ({SelectedCsrPvms.hod.email})
                            </>
                            }
                        </div>

                        {IsGivingTenderApproval &&
                        <>
                      {HodRequired && <div className='form-group mt-2'>
                            <label>HOD <span className='text-danger'>*</span></label>
                            <AsyncSelect cacheOptions loadOptions={loadOptionsforHod} onChange={handleSelectHod} defaultOptions placeholder="Select HOD" />
                        </div>}

                        <div className='form-group mt-2'>
                            <RemarksTemplate changeData={setRemarks} type="csr"/><br/>
                            <label>Remarks {SelectedCsrPvms && SelectedCsrPvms.vandor_perticipate_with_valid_doc && SelectedCsrPvms.vandor_perticipate_with_valid_doc[0]
                         && SelectedCsrPvms.vandor_perticipate_with_valid_doc[0].offered_unit_price && SelectedBidder && SelectedCsrPvms.vandor_perticipate_with_valid_doc.find(item => item.id == SelectedBidder) && parseFloat(SelectedCsrPvms.vandor_perticipate_with_valid_doc.find(item => item.id == SelectedBidder).offered_unit_price )> parseFloat(SelectedCsrPvms.vandor_perticipate_with_valid_doc[0].offered_unit_price) && <span className='text-danger'>*</span>}</label>
                            <div>
                                <textarea className='form-control' value={Remarks} onChange={(e) => setRemarks(e.target.value)}></textarea>
                            </div>
                        </div></>
                        }

                    </div>
                </div>

                {IsGivingTenderApproval &&
                <>
                <div className="text-right">
                    <button className="btn btn-success" disabled={isFormSubmited} onClick={() => handleSubmitApprove(SelectedCsrPvms)}>
                    {CsrPvmsApprovalSteps && SelectedCsrPvms && CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id) &&
                            <>
                            {CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id).step && CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id).step.designation != 'dgms' ?
                            <>{isFormSubmited ? <>{UserApproval.role_key == 'head_clark' ? 'Forwarding...' :`${CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id).step.btnText}...`}</>:
                            <>
                            {UserApproval.role_key == 'head_clark' ?  `Create & Forward`  :  `${CsrPvmsApprovalSteps.find(i => i.id == SelectedCsrPvms.id).step.btnText}`}
                            </>

                            }</>
                            :
                            <>{isFormSubmited ? 'Approving...':'Approve'}</>}
                            </>
                        }
                    </button>
                </div>
                </>
             }
            </div>
        </ModalComponent>

        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
            <h5 className="f-14">CSR Details</h5>
            <div className='d-flex'>
                <Paginate setPage={setPage} Page={Page} Links={CsrNavLink}/>
                {UserApproval && UserApproval.role_key == "head_clark" && <a class="nav-link" href={window.app_url+'/csr-cover-letter'}>
                    <button class="btn-icon btnc btn-custom">
                        <i class="fa fa-plus btn-icon-wrapper"></i> Create Cover Letter
                    </button>
                </a>}
            </div>

        </div>
        <div className='d-flex my-2 justify-content-end mx-2 align-items-center gap-2'>
            <div className='pr-2'>
                <label>Per Page</label>
            </div>
            <div>
                <select className="form-control" value={PerPage} onChange={e => setPerPage(e.target.value)}>
                    <option value={10}>10</option>
                    <option value={25}>25</option>
                    <option value={50}>50</option>
                    <option value={100}>100</option>
                </select>
            </div>
        </div>
        {IsLoadingCsr ?
            <div className="d-flex justify-content-center text-center">
                <div className="ball-pulse w-100">
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                    <div className='spinner-loader'></div>
                </div>
            </div>
            :
            <>
                    <div className="tab-content">
                        <div className="tab-pane " id="tab-animated-0" role="tabpanel">
                           <div className='mb-0'>
                           </div>
                        </div>
                        <div className="tab-pane active" id="tab-animated-1" role="tabpanel">
                            <div className='mb-0'>
                            <table className="table table-bordered">
                            <thead>
                                <tr className='text-left'>
                                    <th>Sl.</th>
                                    <th>Tender</th>
                                    <th className="">
                                        PVMS No
                                    </th>
                                    <th className="">
                                        Nomenclature
                                    </th>
                                    <th className="">
                                        Item Type
                                    </th>
                                    <th className="">
                                        A/U
                                    </th>
                                    <th className="f12">
                                        Quantity
                                    </th>
                                    <th className="">Last Approval</th>
                                    <th className="">Approved Vendor</th>
                                    <th className="">Status</th>
                                    <th className='text-start'>View</th>
                                </tr>
                            </thead>

                            <tbody>
                                {CsrList && CsrList.length>0 && CsrList.map((item,index)=>(
                                    <tr className='text-left'>
                                        <td>{Page == 0 ? index+1+(Page*PerPage) : index+1+(Page*PerPage-PerPage)}</td>
                                        <td >
                                            <div className='d-flex flex-wrap gap-2'>
                                                <div>{item && item.tender && item.tender.tender_no}</div>
                                        {' '}
                                        {item && item.tender && item.tender.deadline && moment().isAfter(moment(item.tender.deadline)) && UserApproval && UserApproval.role_key == CsrPvmsApprovalSteps[index].step.designation && (CsrPvmsApprovalSteps[index].step.designation !== 'hod' || (CsrPvmsApprovalSteps[index].step.designation === 'hod' && item.hod_user === LoggedUser.id)) &&
                                            <div className="badge bg-success text-white f10 ml-1">New</div>
                                        }</div>
                                        </td>
                                        <td>{item.p_v_m_s && item.p_v_m_s.pvms_id}</td>
                                        <td>{item.p_v_m_s && item.p_v_m_s.nomenclature}</td>
                                        <td className='f12'>{item.p_v_m_s && item.p_v_m_s.item_typename.name}</td>
                                        <td className='f12'>{item.p_v_m_s && item.p_v_m_s.unit_name.name}</td>
                                        <td className='f12'>{item.pvms_quantity}</td>
                                        <td className='text-uppercase f12'> <>{item.last_approval && item.last_approval != 'head_clark' ? <>{item.last_approval_rank ? userApprovalRole?.find(i => i.role_key==item.last_approval)?.role_name : '-'}</>:'-' }</></td>
                                        <td className='f12'>{item.selected_bidder && item.selected_bidder && item.selected_bidder.vendor ? item.selected_bidder.vendor.company_name:'-'}</td>
                                        <td className='text-uppercase f12'>{item.status}</td>
                                        <td className='text-center'>
                                            <div>
                                        <td className='text-center d-flex justify-content-start align-items-center border-0'>
                                            <a target='_blank' href={window.app_url+'/csr/download/pdf/'+item.id} className='mr-2 f10' title='CSR Notice Download'>
                                                <i className="fa fa-file-pdf metismenu-icon cursor-pointer f16"> </i>
                                                <br/>
                                                Download
                                            </a>
                                            <div className='cursor-pointer f10' onClick={() => handleViewCsrPvms(item)}>
                                                <i className="fa fa-eye metismenu-icon  cursor-pointer f16"> </i>
                                                <br/>
                                                View
                                            </div>

                                           {item && item.tender && item.tender.deadline && moment().isAfter(moment(item.tender.deadline)) && UserApproval && UserApproval.role_key == CsrPvmsApprovalSteps[index].step.designation && (CsrPvmsApprovalSteps[index].step.designation !== 'hod' || (CsrPvmsApprovalSteps[index].step.designation === 'hod' && item.hod_user === LoggedUser.id)) &&
                                            <div className='ml-2 cursor-pointer f14' onClick={() => handleApproveCsrPvms(item)}>
                                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                                    {/*! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. */}
                                                    <style
                                                        dangerouslySetInnerHTML={{
                                                        __html:
                                                            "\n                                                            svg {\n                                                                fill: #089c14\n                                                            }\n                                                        "
                                                        }}
                                                    />
                                                    <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                                </svg>
                                                <br /><span className='f10'>{UserApproval.role_key == 'head_clark' ? 'Forward'  : 'Approve'}</span>
                                            </div>}
                                            </td>
                                            </div>
                                        </td>
                                    </tr>
                                ))}

                            </tbody>

                            </table>
                            </div>
                        </div>
                    </div>
                {/* </>} */}
            </>
        }
        <div>
            <Paginate setPage={setPage} Page={Page} Links={CsrNavLink}/>
        </div>
        </>
    )
}

if (document.getElementById('react-csr-details')) {
    createRoot(document.getElementById('react-csr-details')).render(<CsrDetails />)
}
