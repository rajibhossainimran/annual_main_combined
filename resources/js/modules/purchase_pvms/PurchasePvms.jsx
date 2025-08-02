import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import ModalComponent from '../../componants/ModalComponent';
import Paginate from '../../componants/Paginate';
import axios from './../util/axios';

export default function PurchasePvms() {
    const [Page, setPage] = useState(0)
    const [PerPage, setPerPage] = useState(10)
    const [PurchasePvmsListLinks, setPurchasePvmsListLinks] = useState()
    const [IsLoading, setIsLoading] = useState(true)
    const [IsShowModal, setIsShowModal] = useState(false)
    const [PurchasePvmsOrderItem, setPurchasePvmsOrderItem] = useState()
    const [PurchasePvmsList, setPurchasePvmsList] = useState()
    const [isFormSubmited, setIsFormSubmited] = useState(false)
    const [isPurchaseUnitStockLoading, setIsPurchaseUnitStockLoading] = useState(false)
    const [UnitStockPvms, setUnitStockPvms] = useState('')
    const [isConfirmFormSubmited, setIsConfirmFormSubmited] = useState(false)
    const [PurchaseType, setPurchaseType] = useState('');
    const [GivePurchasePvmsOrderApproval, setGivePurchasePvmsOrderApproval] = useState(false);
    const [IsDelverPvms, setIsDelverPvms] = useState(false);
    const [UserApproval, setUserApproval] = useState();
    const [UserInfo, setUserInfo] = useState('');

    useEffect(() => {

        setIsLoading(true)
        axios.get(window.app_url + '/purchase-order-list').then((res) => {
            let response = res.data;
            console.log(response.data);
            setPurchasePvmsList(response.data);
            setPurchasePvmsListLinks(response.links);
            setIsLoading(false)
        })
        axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
            if (res.data.user_approval_role) {
                setUserApproval(res.data.user_approval_role);
            }
        })
    }, []);


    useEffect(() => {
        if (Page > 0 || PerPage != 10 || PurchaseType) {
            setIsLoading(true)
            axios.get(window.app_url + `/purchase-order-list?page=${Page}&limit=${PerPage}&type=${PurchaseType}`).then((res) => {
                let response = res.data;

                setPurchasePvmsList(response.data);
                setPurchasePvmsListLinks(response.links);
                setIsLoading(false)
            })
        }

    }, [Page, PerPage, PurchaseType]);

    useEffect(() => {
        if (!IsShowModal) {
            setGivePurchasePvmsOrderApproval(false);
            setIsDelverPvms(false);
            setUnitStockPvms('');
        }
    }, [IsShowModal])

    const handleApprovePurchasePvmsOrder = (item) => {
        setGivePurchasePvmsOrderApproval(true);
        handleClickShowDetails(item);
    }

    const handleForwardPurchasePvmsOrder = (item) => {
        item.purchase_pvms.forEach(pvmsItem => {
            console.log("Item Typename:", pvmsItem.pvms.item_typename);
            console.log("ID:", pvmsItem.pvms.id);
        });
    };

    const pvmsStockUnit = (id) => {
        setIsPurchaseUnitStockLoading(true);
        axios.get(window.app_url + '/purchase-type-unit-stock/' + id).then((res) => {
            setUnitStockPvms(res.data);
            setIsPurchaseUnitStockLoading(false);
        });
    }

    const handleClickShowDetails = (item) => {
        pvmsStockUnit(item.id);
        if (item.status == 'pending') {
            setPurchasePvmsOrderItem({ ...item, purchase_pvms: item.purchase_pvms.map(i => { return { ...i, status: 'approved' } }) })
        } else {
            setPurchasePvmsOrderItem(item)
        }
        setIsShowModal(true)
    }

    const handleClickAfmsdDelivery = (item) => {
        pvmsStockUnit(item.id);
        setIsDelverPvms(true);
        if (item.status == 'pending') {
            setPurchasePvmsOrderItem({ ...item, purchase_pvms: item.purchase_pvms.map(i => { return { ...i, status: 'approved' } }) })
        } else {
            setPurchasePvmsOrderItem({ ...item, purchase_pvms: item.purchase_pvms.map(i => { return { ...i, deliver_today: '', batchPvms: '' } }) })
        }
        setIsShowModal(true)
    }

    const handleSubmitApprove = (item) => {
        if (!item || (item && item.purchase_pvms && item.purchase_pvms.filter(i => i.status == 'approved').length == 0)) {
            Swal.fire({
                icon: 'error',
                text: 'No Item Selected for Purchase PVMS Approval?',
                // showCancelButton: true,
                // confirmButtonText: 'Yes, Approve Now',
                // cancelButtonText: 'No, cancel',
                // reverseButtons: true
            }).then((r) => {
                return;
            })

        } else {
            Swal.fire({
                icon: 'warning',
                text: 'Do you want to approve now ?',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve Now',
                cancelButtonText: 'No, cancel',
                reverseButtons: true
            }).then((r) => {
                if (r.isConfirmed) {
                    setIsFormSubmited(true);
                    axios.post(window.app_url + '/purchase-order-list-approval', item).then((res) => {
                        console.log(res.data);
                        window.location.reload();
                    })
                }
            })
        }

    }

    const handleChangeDeliverQty = (e, item, index, key) => {
        if (key == 'batch') {
            setPurchasePvmsOrderItem(prev => {
                let copy = { ...prev };
                copy.purchase_pvms[index].batchPvms = e.target.value;
                return copy;
            })
        } else {
            let maxval = item.request_qty - item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0);
            if (!e.target.value) {
                setPurchasePvmsOrderItem(prev => {
                    let copy = { ...prev };
                    copy.purchase_pvms[index].deliver_today = e.target.value;
                    return copy;
                })

                return;
            }
            if (e.target.value > 0 && e.target.value <= maxval) {
                setPurchasePvmsOrderItem(prev => {
                    let copy = { ...prev };
                    copy.purchase_pvms[index].deliver_today = e.target.value;
                    return copy;
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: "You can not deliver more then issued quantity",
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }
        }
    }

    const handleConfirmDelivery = (item) => {
        let number_of_pvms = 0;
        item.purchase_pvms.forEach(element => {
            if (element.deliver_today && element.deliver_today > 0) {
                number_of_pvms++;
            }
        });

        if (number_of_pvms == 0) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: "No pvms quantity is given for delivery.",
                // footer: '<a href="">Why do I have this issue?</a>'
            })

            return;
        }

        Swal.fire({
            icon: 'warning',
            text: 'Are you sure to deliver pvms of given quantity?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                setIsConfirmFormSubmited(true);
                axios.post(window.app_url + '/purchase-pvms-delivery', item).then((res) => {
                    console.log(res.data);
                    window.location.reload();
                });
                // setIsConfirmFormSubmited(false);

            }
        })

    }

    const handleChangePurchaseItemSelection = (id, value) => {
        if (id) {
            setPurchasePvmsOrderItem(prev => {
                let copy = { ...prev };
                let findIndex = copy.purchase_pvms.findIndex(i => i.id == id);

                copy.purchase_pvms[findIndex] = { ...copy.purchase_pvms[findIndex], status: value ? 'approved' : 'rejected' }
                return copy;
            })
        } else {
            setPurchasePvmsOrderItem(prev => {
                let copy = { ...prev };
                copy.purchase_pvms = copy.purchase_pvms.map(i => value ? { ...i, status: 'approved' } : { ...i, status: 'rejected' })
                return copy;
            })
        }
    }


    return (
        <>

            <ModalComponent
                show={IsShowModal}
                size={"xl"}
                handleClose={() => setIsShowModal(false)}
                handleShow={() => setIsShowModal(true)}
                modalTitle={
                    <div className='bg-success p-2 text-white f14'>
                        {PurchasePvmsOrderItem && PurchasePvmsOrderItem.purchase_item_type == "issued" && 'Issue'} Voucher No
                        <span className='bg-white py-1 px-3 my-2 mx-2 text-dark boder-radius-25'>{PurchasePvmsOrderItem && PurchasePvmsOrderItem.purchase_number}</span>
                    </div>}
            >

                {PurchasePvmsOrderItem &&
                    <table className='table table-bordered mt-2'>
                        <thead>
                            <tr className=''>
                                {GivePurchasePvmsOrderApproval && <th>
                                    <div className="position-relative custom-control custom-checkbox">
                                        <input name="check" id="exampleCheck" type="checkbox"
                                            checked={PurchasePvmsOrderItem && PurchasePvmsOrderItem.purchase_pvms &&
                                                !PurchasePvmsOrderItem.purchase_pvms.find(i => i.status != 'approved')
                                            }
                                            onChange={(e) => handleChangePurchaseItemSelection('', e.target.checked)}
                                            className="custom-control-input" />
                                        <label for="exampleCheck" class="custom-control-label">All</label>
                                    </div>
                                </th>}
                                <th>Sl.</th>
                                <th>Demand No.</th>
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Itme Type</th>
                                {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                    <>
                                        <th>Unit Stock</th>
                                    </>
                                }
                                {!GivePurchasePvmsOrderApproval && UserApproval && (UserApproval.role_key == 'oic' || UserApproval.role_key == 'cmh_clark') &&
                                    <th>Status</th>
                                }
                                <th className='text-right pr-2'>Issued Qty</th>
                                {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                    <>
                                        <th>Prev. Delivered Qty</th>
                                        {IsDelverPvms &&
                                            <>
                                                <th>Batch</th>
                                                <th>Delivery Qty</th>
                                            </>
                                        }
                                        <th>Intransit Qty</th>
                                        <th>Total Due</th>
                                        {/* <th></th> */}
                                    </>
                                }
                            </tr>
                        </thead>
                        <tbody>
                            {PurchasePvmsOrderItem && PurchasePvmsOrderItem.purchase_pvms.map((item, index) => (
                                <>
                                    {((UserApproval && (UserApproval.role_key == 'oic' || UserApproval.role_key == 'cmh_clark')) || (item.status == 'approved')) &&
                                        <tr>
                                            {GivePurchasePvmsOrderApproval && <td>
                                                <div className="position-relative custom-control custom-checkbox">
                                                    <input className="form-check-input input-check-accent" type="checkbox"
                                                        checked={item.status == 'approved'}
                                                        onChange={(e) => handleChangePurchaseItemSelection(item.id, e.target.checked)}
                                                        id={`checkboxNoLabel_${index}`}
                                                        value="" aria-label="..." />
                                                </div>
                                            </td>}
                                            <td>{index + 1}</td>
                                            <td>{item.demand.uuid}</td>
                                            <td>{item.pvms.pvms_id}</td>
                                            <td>{item.pvms.nomenclature}</td>
                                            <td>{item?.pvms?.item_typename?.name}</td>
                                            {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                                <>
                                                    <td>{isPurchaseUnitStockLoading ? '...' : <>
                                                        {UnitStockPvms &&
                                                            UnitStockPvms[index].stock ? UnitStockPvms[index].stock : 0
                                                        }
                                                    </>}</td>
                                                </>
                                            }
                                            {!GivePurchasePvmsOrderApproval && UserApproval && (UserApproval.role_key == 'oic' || UserApproval.role_key == 'cmh_clark') &&
                                                <td className='text-uppercase'>{item.status}</td>
                                            }
                                            <td className='text-right pr-2'>{item.request_qty}</td>
                                            {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                                <>
                                                    <td>
                                                        {/* {item.purchase_delivery.reduce((prev,curr,index) => prev + curr.delivered_qty ,0)} */}
                                                        {item.received_qty ? item.received_qty : 0}
                                                    </td>
                                                    {IsDelverPvms &&
                                                        <>
                                                            <td>
                                                                <select className='form-control' onChange={(e) => handleChangeDeliverQty(e, item, index, 'batch')} value={item.batchPvms}>
                                                                    <option>Select Batch</option>
                                                                    {
                                                                        item.batch_pvms.map(batch_pvms => (
                                                                            <option value={batch_pvms.id}>{`${batch_pvms.batch.batch_no} (Exp: ${batch_pvms.expire_date})`}</option>
                                                                        ))
                                                                    }
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input className='form-control' type='number' value={item.deliver_today} onChange={(e) => handleChangeDeliverQty(e, item, index, 'qty')} readOnly={(item.request_qty == item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0)) || !item.batchPvms} min={0} max={item.request_qty - item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0)} />
                                                            </td>
                                                        </>
                                                    }
                                                    <td>{item.purchase_delivery.reduce((prev, curr) => {
                                                        if (curr.is_received != 0) {
                                                            return prev + 0;
                                                        } else {
                                                            return prev + curr.delivered_qty;
                                                        }

                                                    }, 0)}</td>
                                                    <td>{item.request_qty - item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0)}</td>
                                                    {/* <th></th> */}
                                                </>
                                            }
                                        </tr>
                                    }
                                </>
                            ))}
                        </tbody>
                    </table>}
                <div className='row mb-2'>

                </div>
                {IsDelverPvms && UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                    <div className="text-right">
                        <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmDelivery(PurchasePvmsOrderItem)}>
                            <>{isConfirmFormSubmited ? `Confirm...` : `Confirm`}</>
                        </button>
                    </div>
                }
                {PurchasePvmsOrderItem && GivePurchasePvmsOrderApproval &&
                    <div className="text-right">
                        <button className="btn btn-success" disabled={isFormSubmited} onClick={() => handleSubmitApprove(PurchasePvmsOrderItem)}>
                            <>{isFormSubmited ? `Approving...` : `Approve`}</>
                        </button>
                    </div>
                }
            </ModalComponent>

            <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                <h5 className="f-14">Item List for Demand</h5>
                <div>
                    <Paginate setPage={setPage} Page={Page} Links={PurchasePvmsListLinks} />
                </div>
            </div>
            <div className='d-flex justify-content-between'>

                {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' ?
                    <div class="d-flex gap-2 mx-2 align-items-center">
                        {/* Unit
                <select class="form-control type ml-2"  name="type" onChange={(e) => setPurchaseType(e.target.value)} value={PurchaseType}>
                    <option value="">Select</option>
                { UserApproval && (UserApproval.role_key == 'oic' || UserApproval.role_key == 'cmh_clark') &&
                    <option value="lp">LP</option>
                }
                    <option value="issued"> Issue</option>
                    <option value="on-loan"> On Loan </option>
                    <option value="notesheet"> Notesheet</option>
                </select> */}
                    </div>
                    :
                    <div class="d-flex gap-2 mx-2 align-items-center">
                        Type
                        <select class="form-control type ml-2" name="type" onChange={(e) => setPurchaseType(e.target.value)} value={PurchaseType}>
                            <option value="">Select</option>
                            {UserApproval && (UserApproval.role_key == 'oic' || UserApproval.role_key == 'cmh_clark') &&
                                <option value="lp">LP</option>
                            }
                            <option value="issued"> Issue</option>
                            <option value="on-loan"> On Loan </option>
                            <option value="notesheet"> Notesheet</option>
                        </select>
                    </div>}

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
            </div>

            <table className="table table-bordered">
                <thead>
                    <tr className=''>
                        <th>Sl.</th>
                        <th className="">Ref. No</th>
                        {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' && <th className="">
                            Unit
                        </th>}
                        <th className="">Type</th>
                        <th className="">Unit</th>
                        <th className="">Send To</th>
                        <th className="">Status</th>
                        <th className="">Total Item</th>
                        <th className="text-left width13-percent">Action</th>
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
                    {PurchasePvmsList && PurchasePvmsList.map((item, index) => (
                        <tr className=''>
                            <td>{Page == 0 ? index + 1 + (Page * PerPage) : index + 1 + (Page * PerPage - PerPage)}</td>
                            <td><div className='d-flex gap-1'>
                                {item.purchase_number}
                                {item.status == 'pending' && UserApproval && UserApproval.role_key == 'oic' && <span className="badge bg-success text-white ml-1">New</span>}
                            </div></td>
                            {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' && <td className="">
                                {item.dmd_unit.name}
                            </td>}
                            <td>{item.purchase_item_type}</td>
                            <td>{item.dmdUnit ? item.dmdUnit.name : 'N/A'}</td>
                            <td>{item.sendTo ? item.sendTo.name : 'N/A'}</td>
                            <td className='text-uppercase'>{item.status}</td>

                            <td>
                                {item.purchase_pvms.length}

                            </td>
                            <td className='text-center d-flex justify-content-start align-items-center'>
                                {/* {item.status != 'pending' && <a target='_blank' href={window.app_url+'/notesheet/download/pdf/'+item.id} className='mr-2' title='Notesheet Notice Download'>
                                <i className="fa fa-file-pdf metismenu-icon cursor-pointer f20"> </i>
                                <br/>
                                Download
                            </a>} */}
                                <div className='cursor-pointer' onClick={() => handleClickShowDetails(item)}>
                                    <i className="pe-7s-note2 metismenu-icon cursor-pointer f20" > </i>
                                    <br />
                                    View
                                </div>

                                <div className="cursor-pointer ml-3" onClick={() => window.open(`/issue-order/download/pdf/${item.id}`, "_blank")}>
                                    <i className="pe-7s-file metismenu-icon cursor-pointer f20"></i>
                                    <br />
                                    PDF View
                                </div>

                                {/* <div className="cursor-pointer ml-3" onClick={() => handleApprovePurchasePvmsOrder(item)}>
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                        <style
                                            dangerouslySetInnerHTML={{
                                                __html: "\n svg { fill: #089c14 }\n",
                                            }}
                                        />
                                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                    </svg>
                                    <br />
                                    Forward
                                </div> */}

                                {/* {item.purchase_item_type == 'issued' && UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                <div className='ml-2 cursor-pointer' onClick={()=> handleClickAfmsdDelivery(item)}>
                                    <i className="fa fa-arrow-right cursor-pointer f20" > </i>
                                    <br/>
                                    Delivery
                                </div>
                            } */}

                                {item.status == 'pending' && UserApproval && UserApproval.role_key == 'oic' &&
                                    <div className='ml-2 cursor-pointer' onClick={() => handleApprovePurchasePvmsOrder(item)}>
                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                            <style
                                                dangerouslySetInnerHTML={{
                                                    __html:
                                                        "\n                                                            svg {\n                                                                fill: #089c14\n                                                            }\n                                                        "
                                                }}
                                            />
                                            <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                        </svg>
                                        <br />Approve
                                    </div>
                                }
                            </td>
                        </tr>
                    ))}

                </tbody>

            </table>
            <div>
                <Paginate setPage={setPage} Page={Page} Links={PurchasePvmsListLinks} />
            </div>
        </>
    )
}

if (document.getElementById('react-purchase-pvms')) {
    createRoot(document.getElementById('react-purchase-pvms')).render(<PurchasePvms />)
}
