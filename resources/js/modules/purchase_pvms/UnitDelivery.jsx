import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import ModalComponent from '../../componants/ModalComponent';
import Paginate from '../../componants/Paginate';
import axios from './../util/axios';
import AsyncSelect from 'react-select/async';
import DatePicker from "react-datepicker";


export default function UnitDelivery() {
    const [Unit, setUnit] = useState('')
    const [Branch, setBranch] = useState('')
    const [financialYears, setFinancialYears] = useState([]);
    const [financialYear, setFinancialYear] = useState('');
    const [isPurchaseUnitStockLoading, setIsPurchaseUnitStockLoading] = useState(false)
    const [UnitStockPvms, setUnitStockPvms] = useState('')
    const [SelectVoucherItem, setSelectVoucherItem] = useState('');
    const [UserInfo, setUserInfo] = useState('');
    const [isConfirmFormSubmited, setIsConfirmFormSubmited] = useState(false);
    const [Isloading, setIsloading] = useState(true);
    const [PvmsList, setPvmsList] = useState('');

    const [issuedData, setIssuedData] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [filteredData, setFilteredData] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [perPage, setPerPage] = useState(10);

    const [filterType, setFilterType] = useState('all');
    const [selectedYear, setSelectedYear] = useState('');

    const [stockControlOfficerData, setStockControlOfficerData] = useState([]);
    // const [searchTerm, setSearchTerm] = useState('');
    // const [currentPage, setCurrentPage] = useState(1);
    // const [perPage, setPerPage] = useState(10);





    useEffect(() => {
        axios.get(`${window.app_url}/settings/financial-years/api`)
            .then((res) => {
                setFinancialYears(res.data)
            })
        axios.get(window.app_url + '/getLoogedUserApproval').then((res) => {
            setUserInfo(res.data);
            setIsloading(false);
        })
    }, [])

    // fetch unit delivery data and show in a table 
    // const fetchIssuedData = (page = 1, search = '') => {
    //     setLoading(true);
    //     axios.get(`${window.app_url}/all-issued-purchases`, {
    //         params: {
    //             page,
    //             per_page: perPage,
    //             search
    //         }
    //     })
    //         .then((res) => {
    //             setIssuedData(res.data.data);
    //             // console.log(res.data.data);
    //             setCurrentPage(res.data.current_page);
    //             setLastPage(res.data.last_page);
    //         })
    //         .finally(() => setLoading(false));
    // };

    const fetchIssuedData = (page = 1, search = '', filter = 'all', year = '') => {
        setLoading(true);
        axios.get(`${window.app_url}/all-issued-purchases`, {
            params: {
                page,
                per_page: perPage,
                search
            }
        })
            .then((res) => {
                let data = res.data.data;

                // Filter by control/non-control
                if (filter === 'control') {
                    data = data.filter(item => item.purchase_pvms[0]?.pvms?.control_types_id === 1);
                } else if (filter === 'noncontrol') {
                    data = data.filter(item => item.purchase_pvms[0]?.pvms?.control_types_id === 2);
                }

                // Filter by financial year
                if (year) {
                    data = data.filter(item => String(item.financial_year?.id) === String(year));
                }

                setIssuedData(data);
                setCurrentPage(res.data.current_page);
                setLastPage(res.data.last_page);
            })
            .finally(() => setLoading(false));
    };





    const handleSearch = (e) => {
        const value = e.target.value;
        setSearchTerm(value);
        fetchIssuedData(1, value);
    };


    useEffect(() => {
        fetchIssuedData(1, searchTerm);
    }, [perPage]);

    useEffect(() => {
        setSelectVoucherItem('');
    }, [Unit]);

    //  end afmsd data end 

    useEffect(() => {
        setSelectVoucherItem('');
    }, [Unit])

    const handleChangeSelectUnit = (item, select) => {
        setUnit(item.data.id);
    }

    const handleChangeSelectBranch = (item, select) => {
        setBranch(item.data.id);
    }

    const handleChangeUnitDeliverQty = (e, item, index, key, batchPvms, batchIndex) => {
        debugger
        if (key == 'batch') {
            setPvmsList(prev => {
                let copy = [...prev];
                copy[index].deliveryBatchList[batchIndex].qty = '';
                copy[index].deliveryBatchList[batchIndex].batchPvms = e.target.value;
                return copy;
            })
        } else {
            let batch = batchPvms.find(i => i.id == PvmsList[index].deliveryBatchList[batchIndex].batchPvms);

            let maxval = item.batch_list.reduce((prev, curr) => prev + curr.available_quantity, 0) - PvmsList[index].deliveryBatchList.reduce((prev, curr, currentIndex) => {
                if (currentIndex != index) {
                    return prev + curr.qty;
                } else {
                    return 0;
                }

            }, 0);

            if (!e.target.value) {
                setPvmsList(prev => {
                    let copy = [...prev];
                    copy[index].deliveryBatchList[batchIndex].qty = e.target.value;
                    return copy;
                })

                return;
            }
            if (e.target.value > 0 && e.target.value <= maxval) {
                setPvmsList(prev => {
                    let copy = [...prev];
                    copy[index].deliveryBatchList[batchIndex].qty = e.target.value;

                    return copy;
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    // title: 'Oops...',
                    text: `You can not deliver more than this batch available quantity. ${batch.batch_no} available quantity is ${batch.available_quantity}`,
                    // footer: '<a href="">Why do I have this issue?</a>'
                })

            }
        }
    }

    const handleSelectPvms = (item, select) => {
        if (PvmsList && PvmsList.find(i => i.id == item.data.id)) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: `This pvms has been already added.`,
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        } else {
            setPvmsList(prev => {
                let copy = [...prev];
                copy.push({ ...item.data, deliveryBatchList: [{ qty: '', batchPvms: '' }] });
                return copy;
            })
        }
    }

    const pvmsStockUnit = (id) => {
        setIsPurchaseUnitStockLoading(true);
        axios.get(window.app_url + '/purchase-type-unit-stock/' + id).then((res) => {
            setUnitStockPvms(res.data);
            setIsPurchaseUnitStockLoading(false);
        });
    }

    const handleChangeDeliverQty = (e, item, index, key, batchPvms, batchIndex, purchaseDelivery, prevQty = 0) => {
        if (key == 'batch') {
            setSelectVoucherItem(prev => {
                let copy = { ...prev };
                copy.purchase_pvms[index].batchPvmsList[batchIndex].qty = '';
                copy.purchase_pvms[index].batchPvmsList[batchIndex].batchPvms = e.target.value;
                return copy;
            })
        } else {
            let batch = batchPvms.find(i => i.id == SelectVoucherItem.purchase_pvms[index].batchPvmsList[batchIndex].batchPvms);
            let today_delivery = item.deliver_today ? (item.deliver_today - prevQty) : 0;
            let maxval = item.request_qty - item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0) - today_delivery;
            let batchAvailable = batch.available_quantity;
            let batch_short = 0;
            debugger
            if (batchAvailable < maxval) {
                batch_short = 1;
                maxval = batchAvailable;
            }
            if (!e.target.value) {
                setSelectVoucherItem(prev => {
                    let copy = { ...prev };
                    copy.purchase_pvms[index].batchPvmsList[batchIndex].qty = e.target.value;
                    copy.purchase_pvms[index].deliver_today = copy.purchase_pvms[index].batchPvmsList.reduce((prev, curr) => {
                        if (curr.qty)
                            return prev + parseInt(curr.qty)
                        else
                            return prev + 0
                    }, 0);
                    return copy;
                })

                return;
            }
            if (e.target.value > 0 && e.target.value <= maxval) {
                setSelectVoucherItem(prev => {
                    let copy = { ...prev };
                    copy.purchase_pvms[index].batchPvmsList[batchIndex].qty = e.target.value;
                    copy.purchase_pvms[index].deliver_today = copy.purchase_pvms[index].batchPvmsList.reduce((prev, curr) => {
                        if (curr.qty)
                            return prev + parseInt(curr.qty)
                        else
                            return prev + 0
                    }, 0);
                    return copy;
                })
            } else {
                if (batch_short) {
                    Swal.fire({
                        icon: 'error',
                        // title: 'Oops...',
                        text: `You can not deliver more than this batch available quantity. ${batch.batch_no} available quantity is ${batch.available_quantity}`,
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                } else {
                    Swal.fire({
                        icon: 'error',
                        // title: 'Oops...',
                        text: "You can not deliver more than issued quantity",
                        // footer: '<a href="">Why do I have this issue?</a>'
                    })
                }

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
                    window.location.reload();
                });
                // setIsConfirmFormSubmited(false);

            }
        })
    }
    const handleConfirmUnitDelivery = (item) => {
        if (!Branch) {
            Swal.fire({
                icon: 'error',
                // title: 'Oops...',
                text: "Please Select the Department/Ward ",
                // footer: '<a href="">Why do I have this issue?</a>'
            })
        }

        let number_of_pvms = 0;
        item.forEach(element => {
            let deliver_today = element.deliveryBatchList.reduce((prev, curr) => prev + curr.qty, 0)
            if (deliver_today > 0) {
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

        let data = {
            'pvmsList': item,
            'branch': Branch
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
                axios.post(window.app_url + '/pvms-unit-stock-out', data).then((res) => {
                    window.location.reload();
                });
                // setIsConfirmFormSubmited(false);

            }
        })

    }

    const handleAddAnotherBatch = (index) => {
        setSelectVoucherItem(prev => {
            let copy = { ...prev };
            copy.purchase_pvms[index].batchPvmsList.push({ qty: '', batchPvms: '' });
            return copy;
        })
    }
    const handleAddAnotherBatchToPvmsList = (index) => {
        setPvmsList(prev => {
            let copy = [...prev];
            copy[index].deliveryBatchList.push({ qty: '', batchPvms: '' });
            return copy;
        })
    }

    const handleSelectVoucherNo = (item, select) => {
        debugger
        if (select.action === "select-option" && item) {
            pvmsStockUnit(item.data.id);
            setSelectVoucherItem({ ...item.data, purchase_pvms: item.data.purchase_pvms.map(i => { return { ...i, deliver_today: '', batchPvmsList: [{ qty: '', batchPvms: '' }] } }) })
        }
    }

    const loadOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/receive-voucher-no-search?keyword=' + inputValue + '&type=issued&unit=' + Unit).then((res) => {
            console.log(res.data);
            const data = res.data;

            let option = [];
            for (const iterator of data) {
                option.push({ value: iterator.id, label: iterator.purchase_number, data: iterator })
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
    const loadPvmsWIthStockOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/pvms-with-stock-api?search=' + inputValue).then((res) => {
            const data = res.data;

            let option = [];
            for (const iterator of data) {
                option.push({ value: iterator.id, label: iterator.pvms_id + ' - ' + iterator.nomenclature + ' - ' + (iterator.pvms_old_name ? iterator.pvms_old_name : 'N/A'), data: iterator })
            }

            callback(option);
        })
    };
    const loadBranchOptions = (inputValue, callback) => {
        axios.get(window.app_url + '/branch-list-api?search=' + inputValue).then((res) => {
            const data = res.data;

            let option = [];
            for (const iterator of data) {
                option.push({ value: iterator.id, label: iterator.name, data: iterator })
            }

            callback(option);
        })
    };

    // handle view button click 
    const handleView = (item) => {
        const rowsHtml = item.purchase_pvms
            .map((pvms, index) => {

                const totalQty = Array.isArray(pvms.batch_pvms)
                    ? pvms.batch_pvms.reduce((sum, b) => sum + (+b.qty || 0), 0)
                    : Array.isArray(pvms.batchPvms)
                        ? pvms.batchPvms.reduce((sum, b) => sum + (+b.qty || 0), 0)
                        : 0;


                return `
        <tr>
          <td>${index + 1}</td>
          <td>${pvms.pvms?.pvms_id ?? 'N/A'}</td>
          <td>${pvms.pvms?.nomenclature ?? 'N/A'}</td>
          <td>${totalQty}</td> 
          <td>${0}</td> 
          <td>${pvms.request_qty}</td> 
          <td>${pvms.received_qty ?? 0}</td> 
        </tr>`;
            })
            .join('');

        const tableHtml = `
    <div class="table-responsive" style="font-size:.8rem; text-align:start;">
      <table class="table table-bordered table-sm text-start">
        <thead>
          <tr>
            <th>#</th>
            <th>Pvms</th>
            <th>Nomenclature</th>
            <th>AFMSD Stock</th>
            <th>Unit Anl Req</th>
            <th>Issue</th>
            <th>Due</th>
          </tr>
        </thead>
        <tbody>${rowsHtml}</tbody>
      </table>
    </div>
  `;

        Swal.fire({
            title: 'Unit Demand Details',
            html: tableHtml,
            width: 1100,
            confirmButtonText: 'Close',
            customClass: { popup: 'text-start' }
        });
    };

    // send id unit delivery to issuePage.jsx 
    const handleIssue = (item) => {
        window.location.href = `/issue-page/${item.id}`;
    };


    useEffect(() => {
        if (UserInfo?.user_approval_role_id === 25 && UserInfo?.sub_organization?.type === 'AFMSD') {
            axios.get(`${window.app_url}/afmsd-pvms-delivery-approval`)
                .then((res) => {
                    console.log(res.data);
                    setStockControlOfficerData(res.data);
                })
                .catch((error) => {
                    console.error("Error loading approvals:", error);
                });
        }
    }, [UserInfo]);




    // afmsd co 
    useEffect(() => {
        if (UserInfo?.user_approval_role_id === 1 && UserInfo?.sub_organization?.type === 'AFMSD') {
            axios.get(`${window.app_url}/afmsd-pvms-delivery-approval-afmsdCo`)
                .then((res) => {
                    console.log(res.data);
                    setStockControlOfficerData(res.data);
                    // console.log(res.data);
                })
                .catch((error) => {
                    console.error("Error loading approvals:", error);
                });
        }
    }, [UserInfo]);

    // groupIncharge 
    useEffect(() => {
        if (UserInfo?.user_approval_role_id === 26 && UserInfo?.sub_organization?.type === 'AFMSD') {
            axios.get(`${window.app_url}/afmsd-pvms-delivery-approval-group-incharge`)
                .then((res) => {
                    console.log(res.data);
                    setStockControlOfficerData(res.data);
                    // console.log(res.data);
                })
                .catch((error) => {
                    console.error("Error loading approvals:", error);
                });
        }
    }, [UserInfo]);




    const filteredDataStockControlOfficer = stockControlOfficerData.filter(item => {
        const search = searchTerm.toLowerCase();
        return (
            item.purchase_number?.toLowerCase().includes(search) ||
            item?.sub_organization?.name?.toLowerCase().includes(search) ||
            item?.purchase_types[0]?.demand?.demand_type?.name?.toLowerCase().includes(search)
        );
    });

    const totalPages = Math.ceil(filteredDataStockControlOfficer.length / perPage);
    const paginatedData = filteredDataStockControlOfficer.slice((currentPage - 1) * perPage, currentPage * perPage);


    // hangle open modal for stockControlOfficer 
    const handleOpenTableModalForStockControlOfficer = (item) => {

        const purchaseName = item.purchase_number;
        const cmhName = item?.sub_organization?.name || 'N/A';
        setTimeout(() => {
            const rowsHtml = item.purchase_types.map((row, index) => {
                const totalDeliveredQty = row.purchase_delivery.reduce(
                    (sum, item) => sum + (item.delivered_qty || 0),
                    0
                );

                return `
        <tr>
            <td class="text-end py-2">${index + 1}</td>
            <td class="text-end py-2">${row?.pvms?.pvms_id ?? ''}</td>
            <td class="text-start py-2">${row?.pvms?.nomenclature ?? ''}</td>
            <td class="text-end py-2">${row?.pvms?.account_unit?.name ?? ''}</td>
            <td class="text-end py-2">${row.request_qty}</td>
            <td class="text-end py-2">${totalDeliveredQty}</td>
            <td class="py-2">
                <textarea 
                    class="form-control form-control-sm" 
                    rows="1"
                    data-row-index="${index}"
                    data-purchase-id="${row?.id ?? ''}"
                    readonly
                >${row?.purchase_delivery[0]?.send_remarks ?? ''}</textarea>
            </td>
        </tr>
    `;
            }).join('');
            Swal.fire({
                title: 'Delivery Details',
                html: `
                    <div style="width: 100%; font-size: 14px;">
                        <div style="text-align: left;">
                            <p style="font-weight: bold; font-size: 16px; margin: 0; margin-bottom:4px;">Voucher No : ${purchaseName}</p>
                            <p style="font-weight: bold; font-size: 14px; margin: 0; margin-bottom:7px;">Unit Name : ${cmhName}</p>
                        </div>

                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>PVMS No.</th>
                                    <th className="text-start" >Nomenclature</th>
                                    <th>A/U</th>
                                    <th>Issued Qty</th>
                                    <th>Intransit Qty</th>
                                    <th>Clerk Remarks</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger ml-2'
                },
                buttonsStyling: false,
                width: '80%',
            }).then(result => {
                if (result.isConfirmed) {
                    // Second alert: confirmation before action
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You want to approve it?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Approve',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-secondary ml-2'
                        },
                        buttonsStyling: false,
                    }).then(async (finalConfirm) => {
                        if (finalConfirm.isConfirmed) {
                            try {
                                const res = await axios.put(`${window.app_url}/afmsd-pvms-delivery-approvals-stockControlOfficer/${item.id}`);
                                Swal.fire({
                                    icon: 'success',
                                    text: 'Delivery data updated successfully!'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } catch (error) {
                                console.error("Update failed:", error);
                                Swal.fire({
                                    icon: 'error',
                                    text: 'Delivery update failed.'
                                });
                            }
                        }
                    });
                }
            });
        }, 0);
    };

    // Handle modal for groupIncharge 

    const handleOpenTableModalForGroupIncharge = (item) => {

        const purchaseName = item.purchase_number;
        const cmhName = item?.sub_organization?.name || 'N/A';
        setTimeout(() => {
            const rowsHtml = item.purchase_types.map((row, index) => {
                const totalDeliveredQty = row.purchase_delivery.reduce(
                    (sum, item) => sum + (item.delivered_qty || 0),
                    0
                );

                return `
        <tr>
            <td class="text-end py-2">${index + 1}</td>
            <td class="text-end py-2">${row?.pvms?.pvms_id ?? ''}</td>
            <td class="text-start py-2">${row?.pvms?.nomenclature ?? ''}</td>
            <td class="text-end py-2">${row?.pvms?.account_unit?.name ?? ''}</td>
            <td class="text-end py-2">${row.request_qty}</td>
            <td class="text-end py-2">${totalDeliveredQty}</td>
            <td class="py-2">
                <textarea 
                    class="form-control form-control-sm" 
                    rows="1"
                    data-row-index="${index}"
                    data-purchase-id="${row?.id ?? ''}"
                    readonly
                >${row?.purchase_delivery[0]?.send_remarks ?? ''}</textarea>
            </td>
        </tr>
    `;
            }).join('');
            Swal.fire({
                title: 'Delivery Details',
                html: `
                    <div style="width: 100%; font-size: 14px;">
                        <div style="text-align: left;">
                            <p style="font-weight: bold; font-size: 16px; margin: 0; margin-bottom:4px;">Voucher No : ${purchaseName}</p>
                            <p style="font-weight: bold; font-size: 14px; margin: 0; margin-bottom:7px;">Unit Name : ${cmhName}</p>
                        </div>

                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>PVMS No.</th>
                                    <th className="text-start" >Nomenclature</th>
                                    <th>A/U</th>
                                    <th>Issued Qty</th>
                                    <th>Intransit Qty</th>
                                    <th>Clerk Remarks</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Approve',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger ml-2'
                },
                buttonsStyling: false,
                width: '80%',
            }).then(result => {
                if (result.isConfirmed) {
                    // Second alert: confirmation before action
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You want to approve it?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Approve',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-secondary ml-2'
                        },
                        buttonsStyling: false,
                    }).then(async (finalConfirm) => {
                        if (finalConfirm.isConfirmed) {
                            try {
                                const res = await axios.put(`${window.app_url}/afmsd-pvms-delivery-approvals-group-incharge/${item.id}`);
                                Swal.fire({
                                    icon: 'success',
                                    text: 'Delivery data updated successfully!'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } catch (error) {
                                console.error("Update failed:", error);
                                Swal.fire({
                                    icon: 'error',
                                    text: 'Delivery update failed.'
                                });
                            }
                        }
                    });
                }
            });
        }, 0);
    };

    // stockControlOfficerData view model code 
    const handleOpenViewForStockControlOfficer = (item) => {
        setTimeout(() => {
            const rowsHtml = item.purchase_types.map((row, index) => {
                const totalDeliveredQty = row.purchase_delivery.reduce(
                    (sum, item) => sum + (item.delivered_qty || 0),
                    0
                );

                return `
        <tr>
            <td class="text-end py-2">${index + 1}</td>
            <td class="text-end py-2">${row?.pvms?.pvms_id ?? ''}</td>
            <td class="text-start py-2">${row?.pvms?.nomenclature ?? ''}</td>
            <td class="text-end py-2">${row?.pvms?.account_unit?.name ?? ''}</td>
            <td class="text-end py-2">${row.request_qty}</td>
            <td class="text-end py-2">${totalDeliveredQty}</td>
            <td class="py-2">
                <textarea 
                    class="form-control form-control-sm" 
                    rows="1"
                    data-row-index="${index}"
                    data-purchase-id="${row?.id ?? ''}"
                    readonly
                >${row?.purchase_delivery[0]?.send_remarks ?? ''}</textarea>
            </td>
        </tr>
    `;
            }).join('');
            Swal.fire({
                title: 'Delivery Details',
                html: `
                    <div style="width: 100%; font-size: 14px;">
                        <table class="table table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>PVMS No.</th>
                                    <th className="text-start" >Nomenclature</th>
                                    <th>A/U</th>
                                    <th>Issued Qty</th>
                                    <th>Intransit Qty</th>
                                    <th>Clerk Remarks</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                        </table>
                    </div>
                `,
                showCancelButton: true,
                cancelButtonText: 'Close',
                showConfirmButton: false,
                customClass: {
                    cancelButton: 'btn btn-success ml-2'
                },
                buttonsStyling: false,
                width: '80%',
            })
        }, 0);
    };

    // send purchase id to get print issueFDF page 
    const handlePrint = (issueItem) => {
        window.open(`/issue/print/${issueItem}`, '_blank', 'noopener,noreferrer');
    }

    return (
        <>
            {Isloading ? (
                <div className='text-center'>
                    <div className="ball-pulse w-100">
                        <div className='spinner-loader'></div>
                        <div className='spinner-loader'></div>
                        <div className='spinner-loader'></div>
                    </div>
                </div>
            ) : UserInfo?.user_approval_role_id === 25 && UserInfo?.sub_organization?.type === 'AFMSD' ? (
                Isloading ? (
                    <div className='text-center'>
                        <div className="ball-pulse w-100">
                            <div className='spinner-loader'></div>
                            <div className='spinner-loader'></div>
                            <div className='spinner-loader'></div>
                        </div>
                    </div>
                ) : (
                    <div>
                        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 className="f-14">Demand Issue Delivery </h5>
                        </div>
                        <div className="d-flex justify-content-between align-items-center mb-2 mt-2">
                            <input
                                type="text"
                                placeholder="Search..."
                                className="form-control w-25"
                                value={searchTerm}
                                onChange={(e) => {
                                    setSearchTerm(e.target.value);
                                    setCurrentPage(1);
                                }}
                            />

                            <select
                                value={perPage}
                                onChange={(e) => {
                                    setPerPage(parseInt(e.target.value));
                                    setCurrentPage(1);
                                }}
                                className="form-select form-select-sm bg-white text-dark border shadow-sm py-1"
                            >
                                <option value={10}>10 per page</option>
                                <option value={20}>20</option>
                                <option value={50}>50</option>
                                <option value={100}>100</option>
                            </select>
                        </div>


                        <div className='p-2'>
                            <table className='table table-bordered mt-2'>
                                <thead>
                                    <tr className=''>
                                        <th>Sl.</th>
                                        <th>Voucher No.</th>
                                        <th>Unit Name</th>
                                        <th>Control-Type</th>
                                        <th>Demand-Type</th>
                                        <th>Date</th>
                                        <th>FY</th>
                                        <th>Items</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stockControlOfficerData && paginatedData.length > 0 ? (
                                        paginatedData.map((item, index) => {
                                            const issueItem = item;

                                            return (
                                                <tr key={index}>
                                                    <td className='py-2'>{index + 1}</td>
                                                    <td>
                                                        {issueItem.purchase_number}
                                                        {issueItem.afmsd_approval === 1 && (
                                                            <span className="badge bg-success ms-2 text-white ml-2">New</span>
                                                        )}
                                                    </td>
                                                    <td>{issueItem?.sub_organization?.name}</td>
                                                    <td>
                                                        {
                                                            issueItem.purchase_types[0]?.pvms?.control_types_id === 1
                                                                ? 'Control Item'
                                                                : issueItem.purchase_types[0]?.pvms?.control_types_id === 2
                                                                    ? 'NonControl Item'
                                                                    : 'Unknown'
                                                        }
                                                    </td>
                                                    <td>{issueItem?.purchase_types[0]?.demand?.demand_type?.name}</td>
                                                    <td>
                                                        {new Date(issueItem.purchase_types[0]?.purchase_delivery[0]?.created_at)
                                                            .toLocaleDateString('en-GB')}
                                                    </td>
                                                    <td>{issueItem?.financial_year?.name}</td>
                                                    <td>{issueItem.purchase_types.length}</td>
                                                    <td className='d-flex justify-content-around align-items-center gap-2'>
                                                        {issueItem.afmsd_approval === 1 && (
                                                            <button className="btn btn-sm btn-success" onClick={() => handleOpenTableModalForStockControlOfficer(issueItem)}>
                                                                Approve
                                                            </button>
                                                        )}
                                                        <button className="btn btn-sm btn-primary me-2" onClick={() => handlePrint(issueItem.id)}>
                                                            Print
                                                        </button>
                                                        <button className="btn btn-sm btn-secondary me-2" onClick={() => handleOpenViewForStockControlOfficer(issueItem)}>view</button>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="9" className="text-center py-3 text-muted">
                                                No issue record for you at this moment.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="d-flex justify-content-center mt-4 gap-2 mx-auto">
                            <button
                                onClick={() => setCurrentPage(p => Math.max(p - 1, 1))}
                                disabled={currentPage === 1}
                                className="btn btn-sm btn-secondary"
                            >
                                Prev
                            </button>

                            <span className="px-2">Page {currentPage} of {totalPages}</span>

                            <button
                                onClick={() => setCurrentPage(p => Math.min(p + 1, totalPages))}
                                disabled={currentPage === totalPages}
                                className="btn btn-sm btn-secondary"
                            >
                                Next
                            </button>
                        </div>

                    </div>)
            ) : UserInfo?.user_approval_role_id === 1 && UserInfo?.sub_organization?.type === 'AFMSD' ? (
                Isloading ? (
                    <div className='text-center'>
                        <div className="ball-pulse w-100">
                            <div className='spinner-loader'></div>
                            <div className='spinner-loader'></div>
                            <div className='spinner-loader'></div>
                        </div>
                    </div>
                ) : (
                    <div>
                        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 className="f-14">Demand Issue Delivery </h5>
                        </div>
                        <div className="d-flex justify-content-between align-items-center mb-2 mt-2">
                            <input
                                type="text"
                                placeholder="Search..."
                                className="form-control w-25"
                                value={searchTerm}
                                onChange={(e) => {
                                    setSearchTerm(e.target.value);
                                    setCurrentPage(1);
                                }}
                            />

                            <select
                                value={perPage}
                                onChange={(e) => {
                                    setPerPage(parseInt(e.target.value));
                                    setCurrentPage(1);
                                }}
                                className="form-select form-select-sm bg-white text-dark border shadow-sm py-1"
                            >
                                <option value={10}>10 per page</option>
                                <option value={20}>20</option>
                                <option value={50}>50</option>
                                <option value={100}>100</option>
                            </select>
                        </div>


                        <div className='p-2'>
                            <table className='table table-bordered mt-2'>
                                <thead>
                                    <tr className=''>
                                        <th>Sl.</th>
                                        <th>Voucher No.</th>
                                        <th>Unit Name</th>
                                        <th>Control-Type</th>
                                        <th>Demand-Type</th>
                                        <th>Date</th>
                                        <th>FY</th>
                                        <th>Items</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stockControlOfficerData && paginatedData.length > 0 ? (
                                        paginatedData.map((item, index) => {
                                            const issueItem = item;

                                            return (
                                                <tr key={index}>
                                                    <td className='py-2'>{index + 1}</td>
                                                    <td>
                                                        {issueItem.purchase_number}
                                                        {issueItem.afmsd_approval === 2 && (
                                                            <span className="badge bg-success ms-2 text-white ml-2">New</span>
                                                        )}
                                                    </td>
                                                    <td>{issueItem?.sub_organization?.name}</td>
                                                    <td>
                                                        {
                                                            issueItem.purchase_types[0]?.pvms?.control_types_id === 1
                                                                ? 'Control Item'
                                                                : issueItem.purchase_types[0]?.pvms?.control_types_id === 2
                                                                    ? 'NonControl Item'
                                                                    : 'Unknown'
                                                        }
                                                    </td>
                                                    <td>{issueItem?.purchase_types[0]?.demand?.demand_type?.name}</td>
                                                    <td>
                                                        {new Date(issueItem.purchase_types[0]?.purchase_delivery[0]?.created_at)
                                                            .toLocaleDateString('en-GB')}
                                                    </td>
                                                    <td>{issueItem?.financial_year?.name}</td>
                                                    <td>{issueItem.purchase_types.length}</td>
                                                    <td className='d-flex justify-content-around align-items-center gap-2'>
                                                        {issueItem.afmsd_approval === 2 && (
                                                            <button className="btn btn-sm btn-success" onClick={() => handleOpenTableModalForStockControlOfficer(issueItem)}>
                                                                Approve
                                                            </button>
                                                        )}
                                                        <button className="btn btn-sm btn-primary me-2" onClick={() => handlePrint(issueItem.id)}>
                                                            Print
                                                        </button>
                                                        <button className="btn btn-sm btn-secondary me-2" onClick={() => handleOpenViewForStockControlOfficer(issueItem)}>view</button>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="9" className="text-center py-3 text-muted">
                                                No issue record for you at this moment.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="d-flex justify-content-center mt-4 gap-2 mx-auto">
                            <button
                                onClick={() => setCurrentPage(p => Math.max(p - 1, 1))}
                                disabled={currentPage === 1}
                                className="btn btn-sm btn-secondary"
                            >
                                Prev
                            </button>

                            <span className="px-2">Page {currentPage} of {totalPages}</span>

                            <button
                                onClick={() => setCurrentPage(p => Math.min(p + 1, totalPages))}
                                disabled={currentPage === totalPages}
                                className="btn btn-sm btn-secondary"
                            >
                                Next
                            </button>
                        </div>

                    </div>)
            ) : UserInfo?.user_approval_role_id === 26 && UserInfo?.sub_organization?.type === 'AFMSD' ? (
                Isloading ? (
                    <div className='text-center'>
                        <div className="ball-pulse w-100">
                            <div className='spinner-loader'></div>
                            <div className='spinner-loader'></div>
                            <div className='spinner-loader'></div>
                        </div>
                    </div>
                ) : (
                    <div>
                        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 className="f-14">Demand Issue Delivery </h5>
                        </div>
                        <div className="d-flex justify-content-between align-items-center mb-2 mt-2">
                            <input
                                type="text"
                                placeholder="Search..."
                                className="form-control w-25"
                                value={searchTerm}
                                onChange={(e) => {
                                    setSearchTerm(e.target.value);
                                    setCurrentPage(1);
                                }}
                            />

                            <select
                                value={perPage}
                                onChange={(e) => {
                                    setPerPage(parseInt(e.target.value));
                                    setCurrentPage(1);
                                }}
                                className="form-select form-select-sm bg-white text-dark border shadow-sm py-1"
                            >
                                <option value={10}>10 per page</option>
                                <option value={20}>20</option>
                                <option value={50}>50</option>
                                <option value={100}>100</option>
                            </select>
                        </div>


                        <div className='p-2'>
                            <table className='table table-bordered mt-2'>
                                <thead>
                                    <tr className=''>
                                        <th>Sl.</th>
                                        <th>Voucher No.</th>
                                        <th>Unit Name</th>
                                        <th>Control-Type</th>
                                        <th>Demand-Type</th>
                                        <th>Date</th>
                                        <th>FY</th>
                                        <th>Items</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stockControlOfficerData && paginatedData.length > 0 ? (
                                        paginatedData.map((item, index) => {
                                            const issueItem = item;

                                            return (
                                                <tr key={index}>
                                                    <td className='py-2'>{index + 1}</td>
                                                    <td>
                                                        {issueItem.purchase_number}
                                                        {issueItem.afmsd_approval === 3 && (
                                                            <span className="badge bg-success ms-2 text-white ml-2">New</span>
                                                        )}
                                                    </td>
                                                    <td>{issueItem?.sub_organization?.name}</td>
                                                    <td>
                                                        {
                                                            issueItem.purchase_types[0]?.pvms?.control_types_id === 1
                                                                ? 'Control Item'
                                                                : issueItem.purchase_types[0]?.pvms?.control_types_id === 2
                                                                    ? 'NonControl Item'
                                                                    : 'Unknown'
                                                        }
                                                    </td>
                                                    <td>{issueItem?.purchase_types[0]?.demand?.demand_type?.name}</td>
                                                    <td>
                                                        {new Date(issueItem.purchase_types[0]?.purchase_delivery[0]?.created_at)
                                                            .toLocaleDateString('en-GB')}
                                                    </td>
                                                    <td>{issueItem?.financial_year?.name}</td>
                                                    <td>{issueItem.purchase_types.length}</td>
                                                    <td className='d-flex justify-content-around align-items-center gap-2'>
                                                        {issueItem.afmsd_approval === 3 && (
                                                            <button className="btn btn-sm btn-success" onClick={() => handleOpenTableModalForGroupIncharge(issueItem)}>
                                                                Approve
                                                            </button>
                                                        )}
                                                        <button className="btn btn-sm btn-primary me-2" onClick={() => handlePrint(issueItem.id)}>
                                                            Print
                                                        </button>
                                                        <button className="btn btn-sm btn-secondary me-2" onClick={() => handleOpenViewForStockControlOfficer(issueItem)}>view</button>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    ) : (
                                        <tr>
                                            <td colSpan="9" className="text-center py-3 text-muted">
                                                No issue record for you at this moment.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="d-flex justify-content-center mt-4 gap-2 mx-auto">
                            <button
                                onClick={() => setCurrentPage(p => Math.max(p - 1, 1))}
                                disabled={currentPage === 1}
                                className="btn btn-sm btn-secondary"
                            >
                                Prev
                            </button>

                            <span className="px-2">Page {currentPage} of {totalPages}</span>

                            <button
                                onClick={() => setCurrentPage(p => Math.min(p + 1, totalPages))}
                                disabled={currentPage === totalPages}
                                className="btn btn-sm btn-secondary"
                            >
                                Next
                            </button>
                        </div>

                    </div>)
            ) : (
                <>
                    {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' ? <>
                        <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 className="f-14">Demand Issue Delivery </h5>
                        </div>
                        {/* <div className='row p-2'>
                            <div className='col-6'>
                                <div className="form-group">
                                    <label>Unit Name</label>
                                    <AsyncSelect cacheOptions name='unit_name' loadOptions={loadUnitOptions} onChange={handleChangeSelectUnit} placeholder="Unit Name" defaultOptions />
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
                            <div className='col-6'>
                                <div className="form-group">
                                    <label>Issue Voucher No</label>
                                    <AsyncSelect cacheOptions loadOptions={loadOptions} onChange={handleSelectVoucherNo} placeholder="Issue Voucher No" />
                                </div>
                            </div>
                        </div> */}

                        {/* show unit data in a table start /////////////////////*/}
                        <div className="container mt-4">
                            {/* <h4>All Issued Vouchers</h4> */}

                            <div className="d-flex flex-wrap gap-5 align-items-center mb-4">

                                {/* Control Type Filter Buttons */}
                                <div className="btn-group mb-2" role="group" aria-label="Control Type">
                                    <button
                                        className={`btn btn-sm ${filterType === 'control' ? 'btn-success' : 'btn-outline-success'}`}
                                        onClick={() => {
                                            setFilterType('control');
                                            fetchIssuedData(1, searchTerm, 'control');
                                        }}
                                    >
                                        Control
                                    </button>
                                    <button
                                        className={`btn btn-sm ${filterType === 'noncontrol' ? 'btn-success' : 'btn-outline-success'}`}
                                        onClick={() => {
                                            setFilterType('noncontrol');
                                            fetchIssuedData(1, searchTerm, 'noncontrol');
                                        }}
                                    >
                                        Non-Control
                                    </button>
                                </div>


                                {/* Financial Year Dropdown */}
                                <div className="d-flex align-items-center gap-2 ml-5 p-2 rounded bg-success text-white shadow-sm">
                                    <label className="mb-0 fw-semibold mr-2">Financial Year:</label>
                                    <select
                                        className="form-select form-select-sm bg-white text-dark border-0 shadow-sm"
                                        style={{ minWidth: '180px', maxWidth: '220px' }}
                                        value={selectedYear}
                                        onChange={(e) => {
                                            const year = e.target.value;
                                            setSelectedYear(year);
                                            fetchIssuedData(1, searchTerm, filterType, year); // use current filters
                                        }}
                                    >
                                        <option value="">All Years</option>
                                        <option value="1">2021–22</option>
                                        <option value="2">2022–23</option>
                                        <option value="3">2023–24</option>
                                        <option value="4">2024–25</option>
                                        <option value="5">2025–26</option>
                                        <option value="5">2026–27</option>
                                    </select>
                                </div>


                            </div>


                            <div className="d-flex justify-content-between mb-3">
                                <input
                                    type="text"
                                    className="form-control w-50"
                                    placeholder="Search by Purchase No"
                                    value={searchTerm}
                                    onChange={handleSearch}
                                />

                                <div className='d-flex justify-content-center align-content-center align-items-center'>
                                    <p className='mt-2 mr-2'>Per page</p>
                                    <div>
                                        <select
                                            className="form-control w-auto ms-3"
                                            value={perPage}
                                            onChange={(e) => setPerPage(parseInt(e.target.value))}
                                        >
                                            <option value="10">10 </option>
                                            <option value="20">20 </option>
                                            <option value="50">50 </option>
                                            <option value="100">100 </option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            {loading ? (
                                <div className='text-center'>
                                    <div className="ball-pulse w-100">
                                        <div className='spinner-loader'></div>
                                        <div className='spinner-loader'></div>
                                        <div className='spinner-loader'></div>
                                    </div>
                                </div>
                            ) : (
                                <>
                                    <table className="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Control Type</th>
                                                <th>FY</th>
                                                <th>Date</th>
                                                <th>Voucher No</th>
                                                <th>Unit Name</th>
                                                <th>Demand Type</th>
                                                <th>Total Items</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {issuedData.length > 0 ? (
                                                issuedData.map((item, index) => {
                                                    console.log(item);
                                                    return (
                                                        <tr key={item.id}>
                                                            <td className='py-2'>{(currentPage - 1) * 10 + index + 1}</td>
                                                            <td>
                                                                {item.purchase_pvms[0]?.pvms && item.purchase_pvms[0].pvms.control_types_id === 1
                                                                    ? 'Control Item'
                                                                    : item.purchase_pvms[0]?.pvms && item.purchase_pvms[0].pvms.control_types_id === 2
                                                                        ? 'NonControl Item'
                                                                        : 'N/A'}
                                                            </td>
                                                            <td>{item?.financial_year?.name}</td>
                                                            <td>{new Date(item.updated_at).toISOString().split('T')[0]}</td>
                                                            <td>
                                                                <div className="d-flex justify-content-between align-items-center">
                                                                    <span>{item.purchase_number}</span>
                                                                    {item.stage === 1 && (
                                                                        <i class="fas fa-check"></i>
                                                                    )}
                                                                </div>
                                                            </td>
                                                            <td>{item.dmd_unit?.name || 'N/A'}</td>
                                                            <td>{item.purchase_pvms[0]?.demand?.demand_type?.name}</td>
                                                            <td>{item.purchase_pvms?.length || 0}</td>
                                                            <td>
                                                                <button className="btn btn-sm btn-primary me-1" onClick={() => handleView(item)}>
                                                                    <i className="fas fa-eye"></i> View
                                                                </button>

                                                                <button className="btn btn-sm btn-secondary mr-2 ml-2"
                                                                    onClick={() => handlePrint(item.id)}
                                                                >
                                                                    <i className="fas fa-print"></i> Print
                                                                </button>
                                                                <button
                                                                    className="btn btn-sm btn-success"
                                                                    onClick={() => handleIssue(item)}
                                                                    disabled={item.stage === 1}
                                                                >
                                                                    <i className="fas fa-share-square"></i> Issue
                                                                </button>
                                                            </td>

                                                        </tr>
                                                    );
                                                })
                                            ) : (
                                                <tr>
                                                    <td colSpan="4" className="text-center">No Data Found</td>
                                                </tr>
                                            )}
                                        </tbody>

                                    </table>


                                    <div className="d-flex justify-content-between">
                                        <button
                                            className="btn btn-sm btn-secondary"
                                            disabled={currentPage === 1}
                                            onClick={() => fetchIssuedData(currentPage - 1, searchTerm)}
                                        >
                                            Prev
                                        </button>
                                        <span>Page {currentPage} of {lastPage}</span>
                                        <button
                                            className="btn btn-sm btn-secondary"
                                            disabled={currentPage === lastPage}
                                            onClick={() => fetchIssuedData(currentPage + 1, searchTerm)}
                                        >
                                            Next
                                        </button>
                                    </div>
                                </>
                            )}
                        </div>

                        {/*////////// show unit data in a table end ///////////////*/}



                        <div className='p-2'>
                            {SelectVoucherItem &&
                                <>
                                    <table className='table table-bordered my-2'>
                                        <thead>
                                            <tr className=''>
                                                <th>Sl.</th>
                                                <th>PVMS No.</th>
                                                <th>Nomenclature</th>
                                                <th>Itme Type</th>
                                                {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                                    <>
                                                        <th>Unit Stock</th>
                                                        <th>Avg. 3 month Previous</th>
                                                    </>
                                                }
                                                <th className='text-right pr-2'>Issued Qty</th>
                                                {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                                    <>
                                                        <th>Prev. Delivered Qty</th>
                                                        <th>Last Received Date</th>
                                                        {/* <th>Batch</th>
                                 <th>Delivery Qty</th> */}
                                                        <th className='text-center'>Delivery</th>
                                                        <th>Intransit Qty</th>
                                                        <th>Total Due</th>
                                                    </>
                                                }
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {SelectVoucherItem && SelectVoucherItem.purchase_pvms.map((item, index) => (
                                                <>
                                                    <tr>
                                                        <td>{index + 1}</td>
                                                        <td>{item.pvms.pvms_id}</td>
                                                        <td>{item.pvms.nomenclature}</td>
                                                        <td>{item?.pvms?.item_typename?.name}</td>
                                                        {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                                            <>
                                                                <td>{isPurchaseUnitStockLoading ? '...' : <>
                                                                    {UnitStockPvms &&
                                                                        UnitStockPvms.find(i => i.pvms_id == item.pvms.id)?.stock ? UnitStockPvms.find(i => i.pvms_id == item.pvms.id)?.stock : 0
                                                                    }
                                                                </>}</td>
                                                                <td>{isPurchaseUnitStockLoading ? '...' : <>
                                                                    {UnitStockPvms &&
                                                                        UnitStockPvms.find(i => i.pvms_id == item.pvms.id)?.stock_three_month ? UnitStockPvms.find(i => i.pvms_id == item.pvms.id)?.stock_three_month : 0
                                                                    }
                                                                </>}</td>
                                                            </>
                                                        }
                                                        <td className='text-right pr-2'>{item.request_qty}</td>
                                                        {UserInfo && UserInfo.sub_organization && UserInfo.sub_organization.type == 'AFMSD' &&
                                                            <>
                                                                <td>
                                                                    {/* {item.purchase_delivery.reduce((prev,curr,index) => prev + curr.delivered_qty ,0)} */}
                                                                    {item.received_qty ? item.received_qty : 0}
                                                                </td>
                                                                <td>dummy date</td>
                                                                <table className='table table-bordered my-2'>
                                                                    <thead>
                                                                        <th>Batch</th>
                                                                        <th>Delivery Qty</th>
                                                                    </thead>
                                                                    <tbody>
                                                                        {item?.batchPvmsList.map((batch, batchIndex) => (
                                                                            <tr>
                                                                                <td>
                                                                                    <select className='form-control'
                                                                                        onChange={(e) => handleChangeDeliverQty(e, item, index, 'batch', item?.batch_pvms, batchIndex, item.purchase_delivery)}
                                                                                        value={batch.batchPvms}
                                                                                    >
                                                                                        <option>Select Batch</option>
                                                                                        {
                                                                                            item?.batch_pvms?.map(batch_pvms => (
                                                                                                <>
                                                                                                    {!item?.batchPvmsList?.find((i, key) => (i.batchPvms == batch_pvms.id && key != batchIndex)) &&
                                                                                                        <option value={batch_pvms.id}>{`${batch_pvms.batch_no} (Exp: ${batch_pvms.expire_date}) (Avl. Qty: ${batch_pvms.available_quantity})`}</option>
                                                                                                    }
                                                                                                </>
                                                                                            ))
                                                                                        }
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input className='form-control' type='number'
                                                                                        value={batch.qty}
                                                                                        onChange={(e) => handleChangeDeliverQty(e, item, index, 'qty', item?.batch_pvms, batchIndex, item.purchase_delivery, batch.qty)}
                                                                                        readOnly={(item.request_qty == item.purchase_delivery.reduce((prev, curr) => prev + curr.delivered_qty, 0)) || !batch.batchPvms}
                                                                                        min={0}
                                                                                    />
                                                                                </td>
                                                                            </tr>
                                                                        ))}
                                                                        <tr>
                                                                            <td>Total</td>
                                                                            <td>{item.deliver_today}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colSpan={2}>
                                                                                <div className='text-right'>
                                                                                    <button className="btn btn-success" onClick={() => handleAddAnotherBatch(index)}>Add Another</button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>

                                                                    </tbody>
                                                                </table>



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
                                                </>
                                            ))}
                                        </tbody>
                                    </table>
                                    <div className="text-right">
                                        <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmDelivery(SelectVoucherItem)}>
                                            <>{isConfirmFormSubmited ? `Confirm...` : `Confirm`}</>
                                        </button>
                                    </div>
                                </>
                            }
                        </div>
                    </>
                        :
                        <>
                            <div className="d-flex justify-content-between align-items-center table-header-bg py-1">
                                <h5 className="f-14">Pmvs Delivery</h5>
                            </div>
                            <div className='row p-2'>
                                <div className='col-6'>
                                    <div className="form-group">
                                        <label>Department/Ward</label>
                                        <AsyncSelect cacheOptions name='ward_name' loadOptions={loadBranchOptions} onChange={handleChangeSelectBranch} placeholder="Department/Ward" defaultOptions />
                                    </div>
                                </div>
                            </div>
                            <div>
                                <table className='table table-bordered mt-2'>
                                    <thead>
                                        <tr className=''>
                                            <th>Sl.</th>
                                            <th>PVMS No.</th>
                                            <th>Nomenclature</th>
                                            <th>Itme Type</th>
                                            <th>Unit Stock</th>
                                            <th className='text-center'>Delivery</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {PvmsList && PvmsList.map((item, index) => (
                                            <tr>
                                                <td>{index + 1}</td>
                                                <td>{item.pvms_id}</td>
                                                <td>{item.nomenclature}</td>
                                                <td>{item?.item_typename?.name}</td>
                                                <td>{item?.batch_list.reduce((prev, curr) => prev + curr.available_quantity, 0)}</td>
                                                <td colSpan={2}>
                                                    <table className='table table-bordered mt-2'>
                                                        <thead>
                                                            <tr className=''>
                                                                <th>Batch</th>
                                                                <th>Qty</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {item?.deliveryBatchList.map((batch, key) => (
                                                                <tr>
                                                                    {/* handleChangeUnitDeliverQty(e,item,index,key,batchPvms,batchIndex) */}
                                                                    <td>
                                                                        <select className='form-control'
                                                                            onChange={(e) => handleChangeUnitDeliverQty(e, item, index, 'batch', item?.batch_list, key)}
                                                                            value={batch.batchPvms}
                                                                        >
                                                                            <option>Select Batch</option>
                                                                            {item?.batch_list.map((each_batch) => (
                                                                                <>
                                                                                    {!item.deliveryBatchList?.find((i, batchIndex) => (i.batchPvms == each_batch.id && key != batchIndex)) &&
                                                                                        <option value={each_batch.id}>{each_batch.batch_no} (Exp: {each_batch.expire_date})  (Avl. Qty: {each_batch.available_quantity})</option>
                                                                                    }
                                                                                </>

                                                                            ))}
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input className='form-control' type='number'
                                                                            value={batch.qty}
                                                                            onChange={(e) => handleChangeUnitDeliverQty(e, item, index, 'qty', item?.batch_list, key)}
                                                                            readOnly={!batch.batchPvms}
                                                                            min={0}
                                                                        />
                                                                    </td>
                                                                </tr>
                                                            ))}
                                                            <tr>
                                                                <td>Total</td>
                                                                <td>{item?.deliveryBatchList.reduce((prev, curr) => curr.qty ? prev + parseInt(curr.qty) : 0, 0)}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colSpan={2}>
                                                                    <div className='text-right'>
                                                                        <button className="btn btn-success"
                                                                            onClick={() => handleAddAnotherBatchToPvmsList(index)}
                                                                        >Add Another</button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                <div className='row my-3'>

                                    <div className='col-md-12 gap-2'>
                                        <b className='mb-2 px-2'>Search PVMS</b>
                                        <div className='px-2'>
                                            <AsyncSelect cacheOptions loadOptions={loadPvmsWIthStockOptions} onChange={handleSelectPvms} value={''} placeholder="PMVS No" />
                                        </div>
                                    </div>
                                </div>
                                {PvmsList && PvmsList.length > 0 &&
                                    <div className="text-right p-2">
                                        <button className="btn btn-success" disabled={isConfirmFormSubmited} onClick={() => handleConfirmUnitDelivery(PvmsList)}>
                                            <>{isConfirmFormSubmited ? `Confirm...` : `Confirm`}</>
                                        </button>
                                    </div>}
                            </div>
                        </>
                    }
                </>)}
        </>
    )
}

if (document.getElementById('react-unit-delivery')) {
    createRoot(document.getElementById('react-unit-delivery')).render(<UnitDelivery />)
}
