import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import ModalComponent from '../../componants/ModalComponent';
import Paginate from '../../componants/Paginate';
import axios from './../util/axios';
import AsyncSelect from 'react-select/async';
import DatePicker from "react-datepicker";

export default function ReceivePvms() {
    const [ReceiveType, setReceiveType] = useState('');
    const [VoucherNo, setVoucherNo] = useState('');
    const [SelectVoucherItem, setSelectVoucherItem] = useState('');
    const [UserInfo, setUserInfo] = useState('');
    const [isConfirmFormSubmited, setIsConfirmFormSubmited] = useState(false)
    const [isPurchaseUnitStockLoading, setIsPurchaseUnitStockLoading] = useState(false)
    const [UnitStockPvms, setUnitStockPvms] = useState('')
    const [financialYears, setFinancialYears] = useState([]);
    const [financialYear, setFinancialYear] = useState('');

    useEffect(() => {
        axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
        })
        axios.get(`${window.app_url}/settings/financial-years/api`)
            .then((res) => {
                setFinancialYears(res.data)
            })
    }, [])

    useEffect(() => {
        setSelectVoucherItem('')
    }, [ReceiveType])

    const handleSelectVoucherNo = (item, select) => {
        debugger
        if (select.action === "select-option" && item) {
            pvmsStockUnit(item.data.id);
            setSelectVoucherItem({
                ...item.data,
                purchase_pvms: item.data.purchase_pvms.map(i => {
                    return {
                        ...i, received_deliver_qty: '', expire_date: '', batch_no: '',
                        purchase_delivery: i?.purchase_delivery?.map(it => { return { ...it, received_qty: it.received_qty ? it.received_qty : it.delivered_qty, waste_qty: it.waste_qty ? it.waste_qty : 0 } })

                    }
                }),
            });
        }
    }

    const pvmsStockUnit = (id) => {
        setIsPurchaseUnitStockLoading(true);
        axios.get(window.app_url + '/purchase-type-unit-stock/' + id).then((res) => {
            setUnitStockPvms(res.data);
            setIsPurchaseUnitStockLoading(false);
        });
    }

    const loadOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/receive-voucher-no-search?keyword=' + inputValue + '&type=' + ReceiveType).then((res) => {
            const data = res.data;

            let option = [];
            for (const iterator of data) {
                option.push({ value: iterator.id, label: iterator.purchase_number, data: iterator })
            }

            callback(option);
        })
    };

    const handleConfirmReceivedDelivery = (item) => {
        let number_of_pvms = 0;
        let wastage_remark_not_given = 0;
        item.purchase_pvms.forEach(element => {
            element.purchase_delivery.forEach(each_delivery => {
                if ((each_delivery.received_qty > 0 || each_delivery.waste_qty > 0) && each_delivery.is_received == 0) {
                    number_of_pvms++;
                    if (each_delivery.waste_qty > 0 && !each_delivery.received_remarks) {
                        wastage_remark_not_given++;
                    }
                }
            })
        });

        if (number_of_pvms == 0) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: "No pvms quantity is given for received delivery.",
                // footer: '<a href="">Why do I have this issue?</a>'
            })

            return;
        }

        if (wastage_remark_not_given > 0) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `${wastage_remark_not_given} items wastage item found. Reamrks is mandatory for those items`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })

            return;
        }

        Swal.fire({
            icon: 'warning',
            text: 'Are you sure to received delivery of pvms of given quantity?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                setIsConfirmFormSubmited(true);
                axios.post(window.app_url + '/purchase-pvms-delivery-received', item).then((res) => {
                    console.log(res.data);
                    window.location.reload();
                });

            }
        })

    }

    const handleChangeDeliveryRecieved = (e, each_delivery, key, index, item, field) => {
        setSelectVoucherItem(prev => {
            let copy = { ...prev };
            if (field == 'received_qty') {

                if (e.target.value < 0 || e.target.value > copy.purchase_pvms[index].purchase_delivery[key].delivered_qty) {
                    Swal.fire({
                        icon: 'error',
                        // title: 'Oops...',
                        text: "Received quantity can not be negetive or greater than deliverd quantity.",
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })

                    return copy;
                }
                if (!e.target.value) {
                    copy.purchase_pvms[index].purchase_delivery[key] = { ...copy.purchase_pvms[index].purchase_delivery[key], waste_qty: '' };
                } else {
                    copy.purchase_pvms[index].purchase_delivery[key] = { ...copy.purchase_pvms[index].purchase_delivery[key], waste_qty: copy.purchase_pvms[index].purchase_delivery[key].delivered_qty - e.target.value };
                }

            } else if (field == 'waste_qty') {
                if (e.target.value < 0 || e.target.value > copy.purchase_pvms[index].purchase_delivery[key].delivered_qty) {
                    Swal.fire({
                        icon: 'error',
                        // title: 'Oops...',
                        text: "Received quantity can not be negetive or greater than deliverd quantity.",
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })

                    return copy;
                }
                if (!e.target.value) {
                    copy.purchase_pvms[index].purchase_delivery[key] = { ...copy.purchase_pvms[index].purchase_delivery[key], waste_qty: '' };
                } else {
                    copy.purchase_pvms[index].purchase_delivery[key] = { ...copy.purchase_pvms[index].purchase_delivery[key], received_qty: copy.purchase_pvms[index].purchase_delivery[key].delivered_qty - e.target.value };
                }
            }

            copy.purchase_pvms[index].purchase_delivery[key] = { ...copy.purchase_pvms[index].purchase_delivery[key], [field]: e.target.value };


            return copy;
        })
    }

    const handleChangeReceivedLpitem = (e, item, index, key) => {

        if (key == 'date') {
            setSelectVoucherItem(prev => {
                let copy = { ...prev };
                copy.purchase_pvms[index].expire_date = e;
                return copy;
            })
        } else if (key == 'batch_no') {
            setSelectVoucherItem(prev => {
                let copy = { ...prev };
                copy.purchase_pvms[index].batch_no = e.target.value;
                return copy;
            })
        } else {
            debugger
            let maxval = item.received_qty ? item.request_qty - item.received_qty : item.request_qty;
            if (!e.target.value) {
                setSelectVoucherItem(prev => {
                    let copy = { ...prev };
                    copy.purchase_pvms[index].received_deliver_qty = e.target.value;
                    return copy;
                })

                return;
            }
            if (parseInt(e.target.value) > 0 && parseInt(e.target.value) <= maxval) {
                setSelectVoucherItem(prev => {
                    let copy = { ...prev };
                    copy.purchase_pvms[index].received_deliver_qty = parseInt(e.target.value);
                    return copy;
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: "You can not local purchase more then issued quantity",
                    // footer: '<a href="">Why do I have this issue?</a>'
                })
            }
        }

    }

    const handleConfirmLpItem = (item) => {

        let number_of_pvms = 0;
        item.purchase_pvms.forEach(element => {
            if (element.received_deliver_qty && element.received_deliver_qty > 0 && element.expire_date && element.batch_no) {
                number_of_pvms++;
            }
        });

        if (number_of_pvms == 0) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: "No pvms quantity is given for lp item receieved.",
                // footer: '<a href="">Why do I have this issue?</a>'
            })

            return;
        }

        Swal.fire({
            icon: 'warning',
            text: 'Are you sure to receive lp item of pvms of given quantity and Expiry date?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((r) => {
            if (r.isConfirmed) {
                setIsConfirmFormSubmited(true);
                axios.post(window.app_url + '/lp-item-received', item).then((res) => {
                    console.log(res.data);
                    window.location.reload();
                });

            }
        })
    }
console.log(SelectVoucherItem);
    return (
        <>

            <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                <h5 className="f-14">Pvms Receive</h5>
            </div>
            <div className='row p-2'>
                <div className='col-6'>
                    <div className="form-group">
                        <label>Type of Voucher</label>
                        <select className="form-control type ml-2" name="type" onChange={(e) => setReceiveType(e.target.value)} value={ReceiveType}>
                            <option value="">Select</option>
                            {/* { UserApproval && (UserApproval.role_key == 'oic' || UserApproval.role_key == 'cmh_clark') && */}
                            <option value="lp">LP</option>
                            {/* } */}
                            <option value="issued"> Issue</option>
                            <option value="on-loan"> On Loan </option>
                            <option value="notesheet"> Notesheet</option>
                        </select>
                    </div>
                </div>
                <div className='col-6'>
                    <label>Financial Years</label>
                    <select className='form-control' required value={financialYear} onChange={(e) => setFinancialYear(e.target.value)}>
                        <option value="">Select</option>
                        {financialYears.map((val, key) => (
                            <option key={key} value={val.id}>{val.name}</option>
                        ))}
                    </select>
                </div>
                {ReceiveType && <div className='col-6'>
                    <div className="form-group">
                        <label>Issue Voucher No</label>
                        <AsyncSelect loadOptions={loadOptions} onChange={handleSelectVoucherNo} placeholder="Voucher No" defaultOptions />
                    </div>
                </div>}

            </div>
            {SelectVoucherItem && SelectVoucherItem.purchase_item_type == 'issued' &&
                <>
                    <div className='mt-2 pl-2'>Issue Voucher No: <b>{SelectVoucherItem && SelectVoucherItem.purchase_number}</b></div>
                    <table className='table table-bordered mt-2'>
                        <thead>
                            <tr className=''>
                                <th>Sl.</th>
                                {/* <th>Demand No.</th> */}
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Itme Type</th>
                                {/* <th>Demand Qty</th> */}
                                <th>Unit Stock</th>
                                {/* <th></th> */}
                                <th className='text-right pr-2'>Issued Qty</th>
                                <th>Prev. Received Qty</th>
                                {/* <th>Received Qty</th> */}
                                <th>Intransit Qty</th>
                                <th>Total Due</th>
                                <th className='text-center'>Delivery Receive</th>
                            </tr>
                        </thead>
                        <tbody>
                            {SelectVoucherItem && SelectVoucherItem.purchase_pvms.map((item, index) => (
                                <>
                                    <tr>
                                        <td>{index + 1}</td>
                                        {/* <td>{item.demand.uuid}</td> */}
                                        <td>{item.pvms.pvms_id}</td>
                                        <td>{item.pvms.nomenclature}</td>
                                        <td>{item?.pvms?.item_typename?.name}</td>
                                        {/* <th>Demand Qty</th> */}
                                        <>
                                            <td>{isPurchaseUnitStockLoading ? '...' : <>
                                                {UnitStockPvms &&
                                                    UnitStockPvms[index].stock ? UnitStockPvms[index].stock : 0
                                                }
                                            </>}</td>
                                        </>
                                        {/* <th></th> */}
                                        <td className='text-right pr-2'>{item.request_qty}</td>
                                        <td>
                                            {/* {item.purchase_delivery.reduce((prev,curr,index) => prev + curr.delivered_qty ,0)} */}
                                            {item.received_qty ? item.received_qty : 0}
                                        </td>
                                        {/* <td>
                                    <input className='form-control' type='number'
                                        value={item.received_deliver_qty}
                                        onChange={(e) => handleChangeReceivedDeliverQty(e,item,index)}
                                        readOnly={item.purchase_delivery.reduce((prev,curr) => {
                                            if(curr.is_received != 0) {
                                                return prev + 0;
                                            } else {
                                                return prev + curr.delivered_qty;
                                            }

                                        } ,0) == 0}
                                        min={0}
                                        max={item.purchase_delivery.reduce((prev,curr) => {
                                            if(curr.is_received != 0) {
                                                return prev + 0;
                                            } else {
                                                return prev + curr.delivered_qty;
                                            }

                                        } ,0)}/>
                                </td> */}
                                        <td>{item.purchase_delivery.reduce((prev, curr) => {
                                            if (curr.is_received != 0) {
                                                return prev + 0;
                                            } else {
                                                return prev + curr.delivered_qty;
                                            }

                                        }, 0)}</td>
                                        <td>{item.request_qty - item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0)}</td>
                                        {/* <th></th> */}
                                        <td>
                                            {item && item.purchase_delivery.length > 0 ?
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Sl.</th>
                                                            <th>Batch</th>
                                                            <th>Delivered Qty</th>
                                                            <th>Received Qty</th>
                                                            <th>Waste Qty</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {item.purchase_delivery.map((each_delivery, key) => (
                                                            <tr>
                                                                <td>{key + 1}</td>
                                                                <td>{each_delivery?.store?.batch?.batch_no}</td>
                                                                <td>{each_delivery.delivered_qty}</td>
                                                                <td>
                                                                    <input type='number' className='form-control' value={each_delivery.received_qty} onChange={(e) => handleChangeDeliveryRecieved(e, each_delivery, key, index, item, 'received_qty')} readOnly={(each_delivery.delivered_qty == (each_delivery.received_qty + each_delivery.waste_qty)) && each_delivery.is_received} />
                                                                </td>
                                                                <td>
                                                                    <input type='number' className='form-control' value={each_delivery.waste_qty} onChange={(e) => handleChangeDeliveryRecieved(e, each_delivery, key, index, item, 'waste_qty')} readOnly={(each_delivery.delivered_qty == (each_delivery.received_qty + each_delivery.waste_qty)) && each_delivery.is_received} />
                                                                </td>
                                                                <td>
                                                                    <textarea className='form-control' value={each_delivery.received_remarks} onChange={(e) => handleChangeDeliveryRecieved(e, each_delivery, key, index, item, 'received_remarks')} readOnly={(each_delivery.delivered_qty == (each_delivery.received_qty + each_delivery.waste_qty)) && each_delivery.is_received}></textarea>
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                                :
                                                <div>No delivery of this PVMS yet</div>
                                            }
                                        </td>
                                    </tr>
                                </>
                            ))}
                        </tbody>
                    </table>
                    <div className='text-right p-2'>
                        <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmReceivedDelivery(SelectVoucherItem)}>
                            <>{isConfirmFormSubmited ? `Confirm...` : `Confirm`}</>
                        </button>
                    </div>
                </>
            }
            {SelectVoucherItem && SelectVoucherItem.purchase_item_type == 'lp' &&

                <>
                    <div className='mt-2 pl-2'>LP Voucher No: <b>{SelectVoucherItem && SelectVoucherItem.purchase_number}</b></div>
                    <table className='table table-bordered mt-2'>
                        <thead>
                            <tr className=''>
                                <th>Sl.</th>
                                {/* <th>Demand No.</th> */}
                                <th>PVMS No.</th>
                                <th>Nomenclature</th>
                                <th>Item Type</th>
                                {/* <th>Demand Qty</th> */}
                                <th>Unit Stock</th>
                                {/* <th></th> */}
                                <th className='text-right pr-2'>Issued Qty</th>
                                <th>Prev. Received Qty</th>
                                <th>Batch No</th>
                                <th>Local Purchase</th>
                                <th>Expiry Date</th>
                                <th>Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            {SelectVoucherItem && SelectVoucherItem.purchase_pvms.map((item, index) => (
                                <>
                                    <tr>
                                        <td>{index + 1}</td>
                                        {/* <td>{item.demand.uuid}</td> */}
                                        <td>{item.pvms.pvms_id}</td>
                                        <td>{item.pvms.nomenclature}</td>
                                        <td>{item?.pvms?.item_typename?.name}</td>
                                        {/* <th>Demand Qty</th> */}
                                        <td>{isPurchaseUnitStockLoading ? '...' : <>
                                            {UnitStockPvms &&
                                                UnitStockPvms[index].stock ? UnitStockPvms[index].stock : 0
                                            }
                                        </>}</td>
                                        {/* <th></th> */}
                                        <td className='text-right pr-2'>{item.request_qty}</td>
                                        <td>
                                            {/* {item.purchase_delivery.reduce((prev,curr,index) => prev + curr.delivered_qty ,0)} */}
                                            {item.received_qty ? item.received_qty : 0}
                                        </td>
                                        <td>
                                            <input className='form-control' type='text'
                                                value={item.batch_no}
                                                onChange={(e) => handleChangeReceivedLpitem(e, item, index, 'batch_no')}
                                            />
                                        </td>
                                        <td>
                                            <input className='form-control' type='number'
                                                value={item.received_deliver_qty}
                                                onChange={(e) => handleChangeReceivedLpitem(e, item, index, 'rec_qty')}
                                                readOnly={item.request_qty == item.received_qty}
                                                min={0}
                                                max={item.request_qty - item.received_qty} />
                                        </td>
                                        <td>
                                            <DatePicker
                                                className="form-control"
                                                name="expire_date"
                                                selected={item.expire_date}
                                                onChange={(date) => handleChangeReceivedLpitem(date, item, index, 'date')}
                                                dateFormat="dd/MM/yyyy"
                                                autoComplete={false}
                                                readOnly={item.request_qty == item.received_qty}
                                                required
                                            />
                                        </td>
                                        <td>{item.request_qty - item.received_qty}</td>
                                    </tr>
                                </>
                            ))}
                        </tbody>
                    </table>
                    <div className='text-right p-2'>
                        <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmLpItem(SelectVoucherItem)}>
                            <>{isConfirmFormSubmited ? `Confirm...` : `Confirm`}</>
                        </button>
                    </div>
                </>
            }
        </>
    )
}

if (document.getElementById('react-purchase-pvms-receive')) {
    createRoot(document.getElementById('react-purchase-pvms-receive')).render(<ReceivePvms />)
}
