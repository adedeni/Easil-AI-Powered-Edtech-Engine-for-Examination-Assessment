import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Eye, EyeOff } from "lucide-react";

export default function Login() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    username: "",
    password: "",
    role: "student"
  });
  const [showPassword, setShowPassword] = useState(false);
  const [rememberMe, setRememberMe] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);

    // Simulate API call
    setTimeout(() => {
      // In a real app, you'd validate credentials against a backend
      // For demo purposes, we'll accept any non-empty credentials
      if (formData.username.trim() && formData.password.trim()) {
        // Store user info in localStorage (in real app, use proper authentication)
        localStorage.setItem('userRole', formData.role);
        localStorage.setItem('username', formData.username);
        
        // Navigate based on role
        switch (formData.role) {
          case 'student':
            navigate('/student');
            break;
          case 'lecturer':
            navigate('/lecturer');
            break;
          case 'admin':
            navigate('/admin');
            break;
          default:
            navigate('/student');
        }
      } else {
        alert('Please enter both username and password');
      }
      setIsLoading(false);
    }, 1000);
  };
  return (
    <div className="min-h-screen flex flex-col lg:flex-row">
      {/* Left Side - Image and Tagline */}
      <div className="lg:w-1/2 w-full relative bg-gradient-to-b from-blue-500 to-blue-600 flex items-center justify-center text-white p-6 sm:p-8 min-h-[40vh] lg:min-h-screen">
        <div className="absolute inset-0 bg-black/30 z-10" />
        <img
          src="/assets/student-login.jpg"
          alt="Student studying"
          className="absolute inset-0 w-full h-full object-cover z-0"
        />
        <div className="relative z-20 max-w-md text-center lg:text-left">
          <h1 className="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4">Your Path to Smarter Learning</h1>
          <p className="text-base sm:text-lg">
            Welcome to Easil! Empowering your learning journey, one assessment at a time.
          </p>
        </div>
      </div>

      {/* Right Side - Form */}
      <div className="lg:w-1/2 w-full flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-[60vh] lg:min-h-screen">
        <div className="bg-white shadow-lg rounded-lg p-6 sm:p-8 w-full max-w-md">
          <div className="text-center mb-4 sm:mb-6">
            <h2 className="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
            <p className="text-gray-600 text-sm sm:text-base">Sign in to your account</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4 sm:space-y-6">
            {/* Role Selection */}
            <div>
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                Login as
              </label>
              <select
                name="role"
                value={formData.role}
                onChange={handleInputChange}
                className="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
                <option value="admin">Administrator</option>
              </select>
            </div>

            {/* Username */}
            <div>
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                Username
              </label>
              <input
                type="text"
                name="username"
                value={formData.username}
                onChange={handleInputChange}
                placeholder="Enter your username"
                className="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
            </div>

            {/* Password */}
            <div>
              <label className="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                Password
              </label>
              <div className="relative">
                <input
                  type={showPassword ? "text" : "password"}
                  name="password"
                  value={formData.password}
                  onChange={handleInputChange}
                  placeholder="Enter your password"
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
            </div>

            {/* Remember Me & Forgot Password */}
            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2">
                <input 
                  type="checkbox" 
                  checked={rememberMe}
                  onChange={(e) => setRememberMe(e.target.checked)}
                  className="accent-blue-600" 
                />
                <span className="text-gray-600">Remember Me</span>
              </label>
              <a href="#" className="text-blue-600 hover:underline">
                Forgot Password?
              </a>
            </div>

            {/* Login Button */}
            <button
              type="submit"
              disabled={isLoading}
              className={`w-full py-3 rounded-lg font-medium transition-colors ${
                isLoading
                  ? 'bg-gray-400 cursor-not-allowed'
                  : 'bg-blue-600 hover:bg-blue-700'
              } text-white`}
            >
              {isLoading ? 'Signing In...' : 'Sign In'}
            </button>
          </form>

          {/* Demo Credentials */}
          <div className="mt-6 p-4 bg-gray-50 rounded-lg">
            <p className="text-xs text-gray-600 text-center mb-2">Demo Credentials:</p>
            <div className="text-xs text-gray-500 space-y-1">
              <p><strong>Student:</strong> student / password</p>
              <p><strong>Lecturer:</strong> lecturer / password</p>
              <p><strong>Admin:</strong> admin / password</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
