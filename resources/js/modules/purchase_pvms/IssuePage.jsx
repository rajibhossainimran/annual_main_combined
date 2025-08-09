import React, { useEffect, useState, useMemo, useCallback } from 'react';
import { createRoot } from 'react-dom/client';
import axios from './../util/axios';
import Select from 'react-select';
import AsyncSelect from 'react-select/async';
import { isCancel } from 'axios';
import Swal from 'sweetalert2';

const Spinner = () => (
    <div className="text-center my-5">
        <div className="ball-pulse w-100">
            <div className="spinner-loader"></div>
            <div className="spinner-loader"></div>
            <div className="spinner-loader"></div>
        </div>
    </div>
);
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
const IssuePage = () => {
    const [item, setItem] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [purchasePvms, setPurchasePvms] = useState([]);
    const [filteredPurchasePvms, setPurchaseFilteredPvms] = useState([]);
    const [selectedPvms, setSelectedPvms] = useState(null);
    const [selectedDate, setSelectedDate] = useState('');

    const [qtyIssue, setQtyIssue] = useState('');
    // for loading table data 
    const [storeRows, setStoreRows] = useState([]);
    const [storeError, setStoreError] = useState('');
    const [storeLoading, setStoreLoading] = useState(false);
    const [dileveredItemQty, setDeliveredItemQty] = useState(0);

    const [remarks, setRemarks] = useState('');

    // for form submision
    //     const [selectedPvms, setSelectedPvms] = useState(null);
    // const [qtyIssue, setQtyIssue] = useState('');
    // const [selectedDate, setSelectedDate] = useState('');



    useEffect(() => {
        const el = document.getElementById('react-issue-page');
        const id = el?.getAttribute('data-id');
        if (!id) return;

        axios
            .get(`/purchase-data/${id}`)
            .then(res => {
                setItem(res.data);
                setPurchasePvms(res.data.purchase_pvms);
            })
            .catch(err => {
                console.error('Error loading item:', err);
                setError('Delivered');
            })
            .finally(() => setLoading(false));
    }, []);

    // filter out purchase pvms 
    // useEffect(() => {
    //     const filtered = purchasePvms.filter(p =>
    //         p.batch_pvms?.some(b => Number(b.qty) > 0)
    //     );
    //     setPurchaseFilteredPvms(filtered);
    // }, [purchasePvms]);

    useEffect(() => {
        const filteredQty = purchasePvms.filter(p =>
            p.batch_pvms?.some(b => Number(b.qty) > 0)
        );

        const checkReceivedItems = async () => {
            const payload = filteredQty.map(item => ({
                purchase_id: item.purchase_id,
                pvms_id: item.pvms.id,
            }));

            try {
                const res = await axios.post('/report/check-received-items', { items: payload });

                const received = res.data; // array of { purchase_id, pvms_id }
                console.log(received);
                const receivedMap = new Set(
                    received.map(item => `${item.purchase_id}_${item.pvms_id}`)
                );

                const finalFiltered = filteredQty.filter(
                    item => !receivedMap.has(`${item.purchase_id}_${item.pvms_id}`)
                );

                setPurchaseFilteredPvms(finalFiltered);

                setDeliveredItemQty(filteredQty.length - finalFiltered.length);
            } catch (err) {
                console.error("Error fetching received items:", err);
            }
        };

        if (filteredQty.length > 0) {
            checkReceivedItems();
            // setPurchaseFilteredPvms(filteredQty);
        } else {
            setPurchaseFilteredPvms([]);
        }
    }, [purchasePvms]);




    // eikhane ami aro akta filter korbo purchasePvms er every item er jonno . ami check korbo purchasePvms er [{purchase_id,pvms_id},{},{}...] every purchase_id and pvms_id use kore backend theke pvms_store table er upor. jekhane purchase_id hobe pvms_store table er issue_voucher_id , pvms_id , where is_received = 1 and where sub_org_id = auth() sub_org_id; jeshokol item pawa jabe bah match hobe sei item gulo ami setPurchaseFilteredPvms ee rakhbo nah and purchasePvms er kototi item match korlo tar ekti quantity ber korbo .





    // set current date ofr issue date 
    useEffect(() => {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const formattedDate = `${yyyy}-${mm}-${dd}`;
        setSelectedDate(formattedDate);
    }, []);


    const loadPvmsOptions = (inputValue, callback) => {
        const filtered = filteredPurchasePvms
            .filter(item =>
                item.pvms.pvms_id?.toLowerCase().includes(inputValue.toLowerCase()) ||
                item.pvms.nomenclature?.toLowerCase().includes(inputValue.toLowerCase())
            )
            .map(item => ({
                value: item.pvms.pvms_id,
                label: `${item.pvms.pvms_id}`,
                full: item,
            }));

        callback(filtered);
    };

    useEffect(() => {
        if (selectedPvms) {
            setQtyIssue(selectedPvms.request_qty || '');
        }
    }, [selectedPvms]);

    // select first item on all pvms 
    useEffect(() => {
        if (filteredPurchasePvms.length > 0 && !selectedPvms) {
            setSelectedPvms(filteredPurchasePvms[0]);
        }
    }, [filteredPurchasePvms]);

    const handlePvmsSelect = (selectedOption) => {
        if (selectedOption) {
            setSelectedPvms(selectedOption.full);
        } else {
            setSelectedPvms(null);
        }
    };


    // for loading data for batch and total stock 
    // useEffect(() => {
    //     if (!selectedPvms) {
    //         setStoreRows([]);
    //         return;
    //     }

    //     const controller = new AbortController();   
    //     async function fetchStoreRows() {
    //         try {
    //             setStoreLoading(true);
    //             setStoreError('');

    //             const { pvms_id } = selectedPvms;
    //             const res = await axios.get(
    //                 `/report/get-pvms-store/50`,
    //                 { signal: controller.signal }
    //             );
    //             setStoreRows(res.data); 
    //         } catch (err) {

    //             console.error(err);
    //             setStoreError('স্টোর ডাটা আনতে সমস্যা হয়েছে');
    //         } finally {
    //             setStoreLoading(false);
    //         }
    //     }
    //     fetchStoreRows();

    //     return () => controller.abort();
    // }, [selectedPvms]);


    useEffect(() => {
        if (!selectedPvms) {
            setStoreRows([]);
            return;
        }

        const controller = new AbortController();

        async function fetchStoreRows() {
            try {
                setStoreLoading(true);
                setStoreError('');

                const { pvms_id } = selectedPvms;
                const res = await axios.get(
                    `/report/get-pvms-store/${pvms_id}`,
                    { signal: controller.signal }
                );

                // Add serial field to each row
                const updatedRows = res.data.map((item, index) => ({
                    ...item,
                    serial: index + 1
                }));

                setStoreRows(updatedRows);

            } catch (err) {
                console.error(err);
                setStoreError('স্টোর ডাটা আনতে সমস্যা হয়েছে');
            } finally {
                setStoreLoading(false);
            }
        }

        fetchStoreRows();
        return () => controller.abort();
    }, [selectedPvms]);


    // total stock value 
    const totalAvailableStock = storeRows.reduce((sum, row) => {
        return sum + (row.available_stock || 0);
    }, 0);

    const { totalQty, receivedQty, remainingQty } = useMemo(() => {
        if (storeRows.length > 0 && storeRows[0]) {
            const totalQty = Number(storeRows[0].contact_total_qty) || 0;
            const receivedQty = Number(storeRows[0].contact_total_received_qty) || 0;
            return {
                totalQty,
                receivedQty,
                remainingQty: totalQty - receivedQty,
            };
        }
        return { totalQty: 0, receivedQty: 0, remainingQty: 0 };
    }, [storeRows]);



    const handleSubmit = (e) => {
        e.preventDefault();

        const formData = {
            purchase_id: item.id,
            sub_org_id: item.sub_org_id,
            pvms_id: selectedPvms.pvms_id || '',
            availableQty: totalAvailableStock,
            qty_issue: qtyIssue,
            qty_Requested: selectedPvms.request_qty,
            dont_check_unit_demand: document.getElementById('dontCheck')?.checked || false,
            unit_name: item?.dmd_unit?.name || '',
            demand_type: item?.purchase_pvms?.[0]?.demand?.demand_type?.name || '',
            remarks: remarks,
            issue_date: selectedDate,
            issue_voucher_no: item?.purchase_number || '',
            batchList: storeRows,
        };

        console.log(formData);
        if (formData.qty_issue <= 0) {
            Swal.fire({
                icon: 'error',
                text: "No pvms quantity is given for delivery.",
            })
            return;
        }
        if (formData.dont_check_unit_demand == false) {
            if (formData.qty_Requested < formData.qty_issue) {
                Swal.fire({
                    icon: 'error',
                    text: "You have entered a quantity greater than the unit demand.",
                })
                return;
            }
        }
        if (Number(formData.qty_issue) > Number(formData.availableQty)) {
            Swal.fire({
                icon: 'error',
                text: "Not enough stock available.",
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
                axios.post(
                    window.app_url + '/item-pvms-issue-delivery',
                    formData,
                    {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        }
                    }
                )
                    .then((res) => {
                        console.log("Response:", res.data);
                        // Remove the issued item from filteredPurchasePvms
                        setPurchaseFilteredPvms(prev => prev.filter(item => item.pvms_id !== selectedPvms.pvms_id));
                        // Set selectedPvms to the next available item or null
                        setSelectedPvms(prev => {
                            const next = filteredPurchasePvms.find(item => item.pvms_id !== selectedPvms.pvms_id);
                            return next || null;
                        });
                        setQtyIssue('');
                        Swal.fire({
                            icon: 'success',
                            text: 'Item issued successfully!',
                        });
                    })
                    .catch((err) => {
                        console.error("Error:", err);
                    });
            }
        });
    };


    // console log value 
    useEffect(() => {
        console.log('Store Rows:', storeRows);
    }, [storeRows]);

    /* ---------- UI ---------- */
    if (loading) return <Spinner />;
    if (error) return <p className="text-danger text-center my-4">{error}</p>;
    if (!item) return <p className="text-center my-4">Item not found.</p>;

    // console.log(item);
    // console.log(purchasePvms);
    // console.log(filteredPurchasePvms);
    return (
        <div className="container mt-4">
            <div className="col-12">
                <div className="row mb-3">
                    <div className="col-md-3">
                        <div className="p-2 border border-success text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                            <div className="text-muted font-weight-bold">Total Items : <span className='fw-bolder text-primary fs-3'>{item.purchase_pvms.length || 0}</span></div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="p-2 border border-success text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                            <div className="text-muted font-weight-bold">AFMSD Stock Items : <span className='fw-bolder text-success fs-3'>{filteredPurchasePvms.length}</span></div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="p-2 border border-success text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                            <div className="text-muted font-weight-bold">AFMSD Stock Out : <span className='fw-bolder text-danger fs-3'>{item.purchase_pvms.length - (filteredPurchasePvms.length + dileveredItemQty)}</span></div>
                        </div>
                    </div>

                    <div className="col-md-3">
                        <div className="p-2 border border-success text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                            <div className="text-muted font-weight-bold"><span className='fw-bolder text-primary fs-3'>{`Issued ${dileveredItemQty} of ${item.purchase_pvms.length || 0}`}</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <form onSubmit={handleSubmit}>
                <div className="row mb-3">
                    <div className="col-md-6">
                        <div className="col-md-12 mb-2">
                            <div className="row">
                                <div className="col-md-4">
                                    <label className="form-label">
                                        FY <span className="text-danger">*</span>
                                    </label>
                                </div>
                                <div className="col-md-8">
                                    <input type="text" className="form-control form-control-sm" value={item.financial_year
                                        .name} readOnly />
                                </div>
                            </div>
                        </div>
                        {/*////////////////////////// selection and change dynamic item details start  //////////////////////// */}

                        <div className="col-md-12 mb-2">
                            <div className="row">
                                <div className="col-md-4">
                                    <label className="form-label">
                                        PVMS No <span className="text-danger">*</span>
                                    </label>
                                </div>
                                <div className="col-md-8">
                                    <AsyncSelect
                                        cacheOptions
                                        defaultOptions={filteredPurchasePvms.map(item => ({
                                            value: item.pvms.pvms_id,
                                            label: `${item.pvms.pvms_id}`,
                                            full: item
                                        }))}
                                        loadOptions={loadPvmsOptions}
                                        value={
                                            selectedPvms
                                                ? {
                                                    value: selectedPvms.pvms.pvms_id,
                                                    label: `${selectedPvms.pvms.pvms_id}`,
                                                    full: selectedPvms
                                                }
                                                : null
                                        }
                                        onChange={handlePvmsSelect}
                                        placeholder="Search PVMS"
                                        isClearable
                                    />
                                </div>
                            </div>
                        </div>


                        {/* Nomenclature */}
                        <div className="col-md-12 mb-2">
                            <div className="row">
                                <div className="col-md-4">
                                    <label className="form-label">Nomenclature</label>
                                </div>
                                <div className="col-md-8">
                                    <textarea
                                        className="form-control form-control-sm"
                                        rows="1"
                                        value={selectedPvms?.pvms.nomenclature || ''}
                                        readOnly
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Group */}
                        <div className="col-md-12 mb-2">
                            <div className="row">
                                <div className="col-md-4">
                                    <label className="form-label">Group</label>
                                </div>
                                <div className="col-md-8">
                                    <input
                                        type="text"
                                        className="form-control form-control-sm"
                                        value={`Group : ${selectedPvms?.pvms.item_groups_id || ''}`}
                                        readOnly
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Account Unit */}
                        <div className="col-md-12 mb-2">
                            <div className="row">
                                <div className="col-md-4">
                                    <label className="form-label">Account Unit</label>
                                </div>
                                <div className="col-md-8">
                                    <input
                                        type="text"
                                        className="form-control form-control-sm"
                                        value={selectedPvms?.pvms?.account_unit?.name || ''}
                                        readOnly
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Quantity Issue */}
                        <div className="col-md-12 mb-2">
                            <div className="row">
                                <div className="col-md-4">
                                    <label className="form-label">Quantity Issue</label>
                                </div>
                                <div className="col-md-8">
                                    <input
                                        type="number"
                                        className="form-control form-control-sm"
                                        value={qtyIssue}
                                        onChange={(e) => setQtyIssue(e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>


                        {/*////////////////////////// selection and change dynamic item details end //////////////////////// */}

                        <div className="col-md-6 d-flex align-items-end mt-2">
                            <div className="form-check">
                                <input
                                    type="checkbox"
                                    className="form-check-input"
                                    id="dontCheck"
                                />
                                <label className="form-check-label" htmlFor="dontCheck">
                                    Don't Check Unit Demand
                                </label>
                            </div>
                        </div>

                    </div>
                    {/* left side part end //////////////////////////// */}
                    <div className='col-md-6'>
                        <div className="row">
                            <div className="col-md-9">
                                <textarea rows="1" cols="12"
                                    className="form-control form-control-sm"
                                    placeholder="Remarks"
                                    value={remarks}
                                    onChange={(e) => setRemarks(e.target.value)}
                                />
                            </div>

                        </div>
                        {/* Row – Unit Name, Demand Type, Issue Date, Voucher No */}
                        <div className="row mb-6">
                            <div className="col-md-12">

                                <div className="row">
                                    <div className="col-md-9 mt-2">
                                        <div className="row mb-2">
                                            <div className="col-md-4">
                                                Unit Name <span className="text-danger">*</span>
                                            </div>
                                            <div className="col-md-8">
                                                <input type="text" className="form-control form-control-sm" value={item?.dmd_unit?.name} readOnly />
                                            </div>
                                        </div>

                                        <div className="row">
                                            <div className="col-md-4">
                                                Demand Type <span className="text-danger">*</span>
                                            </div>
                                            <div className="col-md-8">
                                                <input type="text" className="form-control form-control-sm" value={item?.purchase_pvms[0]?.demand?.demand_type?.name} readOnly />
                                            </div>
                                        </div>

                                    </div>

                                    <div className="col-md-3">
                                        {/* Remarks & Page No */}
                                        <div className="text-end mt-2 text-primary">
                                            <p className='mb-0'><strong>Remarks:</strong> {item.purchase_pvms[0]?.pvms?.remarks || 'Null'}</p>
                                            <p className='mt-0'><strong>Page No:</strong> {item.purchase_pvms[0]?.pvms?.page_no || 'Null'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="col-md-12 mt-4">
                                <div className="row mb-2">
                                    <div className="col-md-3">
                                        Issue Date <span className="text-danger">*</span>
                                    </div>
                                    <div className="col-md-9">
                                        <input
                                            type="date"
                                            className="form-control form-control-sm"
                                            value={selectedDate}
                                            onChange={(e) => setSelectedDate(e.target.value)}
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="col-md-12">
                                <div className="row">
                                    <div className="col-md-3">
                                        Issue Voucher No <span className="text-danger">*</span>
                                    </div>
                                    <div className="col-md-9">
                                        <input type="text" className="form-control form-control-sm" value={item.
                                            purchase_number} readOnly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Submit */}
                <div className="text-center">
                    <button type="submit" className="btn btn-success">
                        Issue
                    </button>
                </div>
            </form>

            {/* display table for total stock and batch  */}

            {/* ---- pvms_store table ---- */}
            <div className="mt-4">
                <p className='app-content-top-title'>AFMSD Stock Summary</p>

                <div className="row mb-2">
                    <div className="col-md-6">
                        <div className="p-2 border border-success text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                            <div className="text-muted font-weight-bold">Avaiable Stock Quantity : <span className='fw-bolder text-primary fs-3'>{totalAvailableStock}</span></div>
                        </div>
                    </div>

                    <div className="col-md-6">
                        <div className="p-2 border border-success text-center d-flex flex-column justify-content-center" style={{ height: '30px' }}>
                            <div className="text-muted font-weight-bold">Contact Quantity : <span className='fw-bolder text-success fs-3'>{remainingQty || 0}</span></div>
                        </div>
                    </div>
                </div>

                {storeLoading && <p>লোড হচ্ছে…</p>}
                {storeError && <p className="text-danger">{storeError}</p>}

                {storeRows.length > 0 ? (
                    <table className="table table-bordered table-sm">
                        <thead className="table-light">
                            <tr>
                                <th>#</th>
                                <th>Expire Date</th>
                                <th>Quantity</th>
                                <th>Supplier Name</th>
                                <th>Contact No</th>
                                <th>CRV</th>
                                <th>Receive Date</th>
                                <th>Receive Qty</th>
                                <th>Batch Serial</th>
                            </tr>
                        </thead>
                        <tbody>
                            {storeRows.map((data, i) => (
                                <tr key={data.id}>
                                    <td>{i + 1}</td>
                                    <td>{data.batch.expire_date}</td>
                                    <td>{data.available_stock}</td>
                                    <td>{data?.workorder_receive_pvms?.workorder_receive?.workorder?.vendor?.company_name}</td>
                                    <td>{data?.workorder_receive_pvms?.workorder_receive?.workorder?.order_no}</td>
                                    <td>{data?.workorder_receive_pvms?.workorder_receive?.crv_no}</td>
                                    <td>{data?.workorder_receive_pvms?.workorder_receive?.receiving_date}</td>
                                    <td>{data?.total_stock_in}</td>
                                    <td>
                                        <input
                                            type="number"
                                            className="form-control form-control-sm text-center"
                                            value={data.serial}
                                            onChange={(e) => {
                                                const updated = [...storeRows];
                                                updated[i].serial = e.target.value;
                                                updated.sort((a, b) => Number(a.serial) - Number(b.serial));
                                                setStoreRows(updated);
                                            }}
                                            onBlur={(e) => {
                                                const updated = [...storeRows];
                                                updated[i].serial = Number(e.target.value);
                                                updated.sort((a, b) => Number(a.serial) - Number(b.serial));
                                                setStoreRows(updated);
                                            }}
                                        />

                                    </td>


                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : !storeLoading && (
                    <p className="text-muted">এই PVMS‑এর জন্য স্টোর ডাটা পাওয়া যায়নি।</p>
                )}
            </div>



        </div>
    );
};


const mountEl = document.getElementById('react-issue-page');
if (mountEl) {
    createRoot(mountEl).render(<IssuePage />);
}
