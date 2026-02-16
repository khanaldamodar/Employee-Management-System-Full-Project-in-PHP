// Email validation
function validateEmail(email) {
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailPattern.test(email);
}

// Password validation (must be at least 8 characters)
function validatePassword(password) {
    return password.length >= 8;
}

// Contact number validation (must be 10 digits & start with 9)
function validateContact(contact) {
    const contactPattern = /^9[0-9]{9}$/;  // Must start with 9 and be exactly 10 digits
    return contactPattern.test(contact);
}

// NID validation (must be 14 digits, allowing only numbers and dash)
function validateNid(nid) {
    const nidPattern = /^[0-9]{11,14}$/;  // Allowing 11 to 14 numbers
    return nidPattern.test(nid);
}

// CV validation (must be a PDF or DOC file)
function validateCV(cvFile) {
    if (!cvFile) return false;
    const allowedExtensions = /(\.pdf|\.doc|\.docx)$/i;
    return allowedExtensions.test(cvFile.name);
}

// Profile picture validation (must be JPG, JPEG, or PNG)
function validateProfilePic(picFile) {
    if (!picFile) return false;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
    return allowedExtensions.test(picFile.name);
}

// Form submission validation
function validateForm() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const contact = document.getElementById("contact").value;
    const nid = document.getElementById("nid").value;
    const birthdayInput = document.getElementById("birthday").value;
    const birthday = new Date(birthdayInput);
    const today = new Date();
    const cvFile = document.getElementById("cv").files[0];
    const picFile = document.getElementById("image").files[0];

    // Age validation (must be at least 18)
    const age = today.getFullYear() - birthday.getFullYear();
    const monthDifference = today.getMonth() - birthday.getMonth();
    const dayDifference = today.getDate() - birthday.getDate();

    if (age < 18 || (age === 18 && (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)))) {
        alert("Employee must be at least 18 years old.");
        return false;
    }

    if (!validateEmail(email)) {
        alert("Please enter a valid email address.");
        return false;
    }

    if (!validatePassword(password)) {
        alert("Password must be at least 8 characters.");
        return false;
    }

    if (!validateContact(contact)) {
        alert("Contact number must be 10 digits and start with 9.");
        return false;
    }

    if (!validateNid(nid)) {
        alert("Invalid National ID format.");
        return false;
    }

    if (!validateCV(cvFile)) {
        alert("Invalid CV format. Please upload a PDF or DOC file.");
        return false;
    }

    if (!validateProfilePic(picFile)) {
        alert("Invalid image format. Please upload a JPG, JPEG, or PNG file.");
        return false;
    }

    return true;
}
