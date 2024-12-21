import React, { useEffect, useState } from 'react';

const Alert = ({type,message,setshowAlert}) => {
    const [fadeOut, setFadeOut] = useState(false); // State to control fade-out effect
    let title = ""
    let classAlert = ""

    switch (type) {
        case "success":
            title = "Sukses"
            classAlert = "alert alert-success alert-dismissible fade show"
            break;
    
        case "info":
            title = "Info"
            classAlert = "alert alert-info alert-dismissible fade show"
            break;
    
        case "warning":
            title = "Warning"
            classAlert = "alert alert-warning alert-dismissible fade show"
            break;
    
        case "danger":
            title = "Error"
            classAlert = "alert alert-error alert-dismissible fade show"
            break;
        default:
            title = "Undefined"
            classAlert = "alert alert-error alert-dismissible fade show"
            break;
    }

    useEffect(() => {
        const timeout = setTimeout(() => {
            setFadeOut(true)
        }, 3000)

        const fadeTimeout = setTimeout(() => {
            setshowAlert(false)
        }, 3500)

        return () => {
            clearTimeout(timeout)
            clearTimeout(fadeTimeout)
        }
        // eslint-disable-next-line
    }, [])

    return(
        <>
            <div 
                className={`${classAlert} ${fadeOut ? "fade-out" : ""}`}
                role="alert" 
                style={{
                    position:"absolute",
                    top:10,
                    right:10,
                    transition: "opacity 0.5s ease-in-out", // Smooth fade-out effect
                    opacity: fadeOut ? 0 : 1, // Control visibility during fade-out
                }}
            >
                <strong>{title}</strong> {message}
                <button type="button" className="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </>
    )
}

export default Alert;