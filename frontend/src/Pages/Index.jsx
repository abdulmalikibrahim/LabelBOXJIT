import React, { useEffect, useState } from 'react';
import axios from 'axios';
import Alert from '../Component/Alert';

const Index = ({ ...props }) => {
    document.title = `Download Label JIT`; // Set the new title
    const API_URL = props.api_url;
    const [dataVendor, setdataVendor] = useState([]);
    const [loading, setLoading] = useState({});
    const [loadingDN, setLoadingDN] = useState({});
    const [typeAlert,settypeAlert] = useState("")
    const [msgAlert,setmsgAlert] = useState("")
    const [showAlert,setshowAlert] = useState(false)
    const [reloadData,setreloadData] = useState(true)

    useEffect(() => {
        const getData = async () => {
            try {
                const response = await axios.get(`${API_URL}/getDataVendor`);
                if (response.status === 200) {
                    setdataVendor(response.data.res);
                } else {
                    console.error(`Error getting data vendor ${response.res}`);
                }
            } catch (error) {
                console.error(error);
            }
        }

        getData();
        setInterval(() => {
            getData();
        }, 1000);
    }, []);

    return (
        <>
            {
                showAlert && 
                <Alert 
                    type={typeAlert} 
                    message={msgAlert} 
                    setshowAlert={setshowAlert}
                />
            }
            <Main>
                <UploadExcel
                    API_URL={API_URL}
                    settypeAlert={settypeAlert}
                    setmsgAlert={setmsgAlert}
                    setshowAlert={setshowAlert}
                    reloadData={reloadData}
                    setreloadData={setreloadData}
                />
                <LoadTable
                    dataVendor={dataVendor}
                    setLoading={setLoading}
                    loading={loading}
                    setLoadingDN={setLoadingDN}
                    loadingDN={loadingDN}
                    API_URL={API_URL}
                />
            </Main>
        </>
    );
}

const Main = ({children}) => {
    return(
        <div className="p-3 bg-body" style={{ height: "100%", minHeight: "100vh", height:"100%" }}>
            <div className="w-100 text-center mt-3">
                <h1 style={{ fontSize: "45pt", textShadow: "2px 2px rgb(0,0,0) !important" }} className="text-light">
                    LABEL JIT BOX
                </h1>
                {children}
            </div>
        </div>
    )
}

const LoadTable = ({dataVendor,setLoading,loading,setLoadingDN,loadingDN,API_URL}) => {
    let nomor = 0;
    
    // Trigger PDF download and show loading animation for the specific vendor
    const handleDownload = (vendor_code, vendor_alias, typeDownload) => {
        const openStatus = async () => {
            const response = await axios.post(`${API_URL}/updateStatus`, {
                vendor_code: vendor_code,
                typeDownload: typeDownload,
            });
    
            if (response.status === 200) {
                // window.print();
                console.log("Success Open Status");
            } else {
                console.error("Failed Open Status");
            }
        }
        openStatus();

        if(typeDownload === "kanban"){
            setLoading((prevLoading) => ({
                ...prevLoading,
                [vendor_code]: true,  // Set loading for specific vendor
            }));
        }else{
            setLoadingDN((prevLoading) => ({
                ...prevLoading,
                [vendor_code]: true,  // Set loading for specific vendor
            }));
        }
    
        const openNewWindow = () => {
            const url = typeDownload === "kanban" 
            ? `printKanban?vendor_code=${vendor_code}&vendor_alias=${vendor_alias}`
            : `${API_URL}/printDN?vendor_code=${vendor_code}&vendor_alias=${vendor_alias}`

            const windowName = "NewWindow"; // Name of the window
            const width = 1200; // Width of the window
            const height = 1000; // Height of the window
            
            // Calculate position to center the window on the screen
            const left = window.screen.width / 2 - width / 2;
            const top = window.screen.height / 2 - height / 2;
            
            // Open the new window with custom width, height, and position
            const newWindow = window.open(
                url,
                windowName,
                `width=${width},height=${height},top=${top},left=${left},resizable=no,scrollbars=yes`
            );
            
            newWindow.onbeforeunload = function () {
                // Close the window when the user navigates away or the download is finished
                console.log('Window is closing after PDF download.');
            };
            
            setTimeout(() => {
                typeDownload === "kanban" ? setLoading(false) : setLoadingDN(false)
            }, 2000);
        };
        openNewWindow()
    };

    return(
        <div className="row d-flex justify-content-center mt-3">
            <div className="col-6">
                <table className="table table-bordered">
                    <thead className="thead-light">
                        <tr className="fw-bold text-light">
                            <th>No</th>
                            <th>Vendor Alias</th>
                            <th>Vendor Code</th>
                            <th>Kanban</th>
                            <th>DN</th>
                        </tr>
                    </thead>
                    <tbody>
                        {
                            dataVendor.map((e, index) => {
                                return (
                                    <tr key={`${index}-${e.vendor_code}`} className="text-light fw-bold">
                                        <td>{(nomor += 1)}</td>
                                        <td>{e.vendor_alias}</td>
                                        <td>{e.vendor_code}</td>
                                        <td>
                                            <button
                                                onClick={() => handleDownload(e.vendor_code, e.vendor_alias, "kanban")}
                                                className="btn btn-sm btn-light me-2"
                                                >
                                                {loading[e.vendor_code] ? (
                                                    <><i className="fas fa-spinner fa-spin"></i> Downloading</>
                                                ) : (
                                                    e.download_kanban === "0" 
                                                    ? <><i className="fas fa-download me-1"></i>Download</> 
                                                    : <><i className="fas fa-check-circle me-1"></i>Downloaded</>
                                                )}
                                            </button>
                                        </td>
                                        <td>
                                            <button
                                                onClick={() => handleDownload(e.vendor_code, e.vendor_alias, "dn")}
                                                className="btn btn-sm btn-light"
                                                >
                                                {loadingDN[e.vendor_code] ? (
                                                    <><i className="fas fa-spinner fa-spin"></i> Downloading</>
                                                ) : (
                                                    e.download_dn === "0" 
                                                    ? <><i className="fas fa-download me-1"></i>Download</> 
                                                    : <><i className="fas fa-check-circle me-1"></i>Downloaded</>
                                                )}
                                            </button>
                                        </td>
                                    </tr>
                                )
                            })
                        }
                    </tbody>
                </table>
            </div>
        </div>
    )
}

const UploadExcel = ({API_URL,settypeAlert,setmsgAlert,setshowAlert,reloadData,setreloadData}) => {
    const [file, setFile] = useState(null)
    const [loadingUpload,setloadingUpload] = useState(false)
    
    const handleFileChange = (e) => {
        setFile(e.target.files[0]); // Store the selected file
    };
    
    const handleSubmit = async (e) => {
        setloadingUpload(true)
        e.preventDefault();
        if (!file) {
            settypeAlert("warning")
            setmsgAlert("Please select a file!")
            setshowAlert(true)
            setloadingUpload(false)
            return
        }
        
        // Prepare the file for upload
        const formData = new FormData();
        formData.append("upload-file", file);
        
        try {
            const response = await axios.post(`${API_URL}/uploadData`,formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data', // Required for file uploads
                    },
                }
            );

            if (response.status === 200) {
                settypeAlert("success")
                setmsgAlert("File uploaded successfully!")
                setreloadData(!reloadData)
            } else {
                settypeAlert("danger")
                setmsgAlert("File upload failed. Please try again.")
            }
        } catch (error) {
            console.error("Error uploading file:", error)
            settypeAlert("danger")
            setmsgAlert("An error occurred while uploading the file.")
        } finally {
            setshowAlert(true)
            setloadingUpload(false)
        }
    };
    
    return(
        <div className="row d-flex justify-content-center mt-5">
            <div className="col-6">
                <form onSubmit={handleSubmit}>
                    <p className='text-start fw-bold text-light mb-1'>Upload File</p>
                    <div className="input-group">
                        <input type="file" className="form-control" accept='.xlsx' onChange={handleFileChange} />
                        <button type='submit' className="btn btn-primary">
                            {
                                !loadingUpload 
                                ? <><i className="fas fa-upload me-1"></i>Upload</> 
                                : <><i className="fas fa-spinner fa-spin me-1"></i>Uploading...</>
                            }
                        </button>
                    </div>
                    <p className='text-light text-start mb-1 mt-2'>Download Template Upload <a href="/Assets/Format Upload.xlsx" target='_blank' className='text-light fw-bold'>disini</a></p>
                </form>
            </div>
        </div>
    )
}

export default Index;
